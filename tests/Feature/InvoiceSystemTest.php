<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\CompanySetting;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentLink;
use App\Models\Service;
use App\Models\User;
use App\Services\InvoiceService;
use App\Services\PaymentGatewayService;
use App\Services\PaymentService;
use App\Services\PdfInvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class InvoiceSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
        $this->staff = User::where('role', 'staff')->first();
        $this->client = Client::first();
    }

    public function test_login_authenticates_user()
    {
        $response = $this->post('/login', [
            'email' => 'admin@assertivlogix.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_admin_can_access_dashboard_and_settings()
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);

        $responseSettings = $this->actingAs($this->admin)->get('/settings');
        $responseSettings->assertStatus(200);
    }

    public function test_staff_cannot_access_settings()
    {
        $response = $this->actingAs($this->staff)->get('/settings');
        $response->assertStatus(403);
    }

    public function test_invoice_creation_calculates_correct_totals()
    {
        $invoiceService = app(InvoiceService::class);

        $data = [
            'client_id' => $this->client->id,
            'invoice_number' => 'INV-TEST-0001',
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'currency' => 'USD',
            'payment_terms' => 'Net 30',
            'discount_type' => 'fixed',
            'discount_value' => 100.00,
            'additional_charges' => 50.00,
            'round_off' => 0.00,
            'status' => 'Sent',
        ];

        $items = [
            ['item_name' => 'Web Development', 'quantity' => 10, 'unit' => 'Hour', 'unit_price' => 100.00, 'tax_rate' => 0], // $1000
            ['item_name' => 'Design', 'quantity' => 1, 'unit' => 'Project', 'unit_price' => 500.00, 'tax_rate' => 0], // $500
        ];

        // Subtotal = $1500, Discount = $100 -> Taxable = $1400. Additional = $50 -> Grand Total = $1450
        $invoice = $invoiceService->createInvoice($data, $items);

        $this->assertEquals(1500.00, $invoice->subtotal);
        $this->assertEquals(100.00, $invoice->discount_amount);
        $this->assertEquals(1400.00, $invoice->taxable_amount);
        $this->assertEquals(1450.00, $invoice->grand_total);
        $this->assertEquals(1450.00, $invoice->balance_due);
        $this->assertEquals('Sent', $invoice->status);
    }

    public function test_payment_updates_invoice_balance_and_status_transitions()
    {
        $invoiceService = app(InvoiceService::class);
        $paymentService = app(PaymentService::class);

        $invoice = $invoiceService->createInvoice([
            'client_id' => $this->client->id,
            'invoice_number' => 'INV-TEST-0002',
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'currency' => 'USD',
            'payment_terms' => 'Net 30',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'status' => 'Sent',
        ], [
            ['item_name' => 'Consulting', 'quantity' => 10, 'unit' => 'Hour', 'unit_price' => 100.00], // $1000
        ]);

        $this->assertEquals(1000.00, $invoice->balance_due);

        // 1. Partial Payment of $400
        $paymentService->recordPayment([
            'invoice_id' => $invoice->id,
            'amount' => 400.00,
            'payment_method' => 'Bank Transfer',
        ]);

        $invoice->refresh();
        $this->assertEquals(400.00, $invoice->paid_amount);
        $this->assertEquals(600.00, $invoice->balance_due);
        $this->assertEquals('Partially Paid', $invoice->status);

        // 2. Full Payment of remaining $600
        $paymentService->recordPayment([
            'invoice_id' => $invoice->id,
            'amount' => 600.00,
            'payment_method' => 'Stripe',
        ]);

        $invoice->refresh();
        $this->assertEquals(1000.00, $invoice->paid_amount);
        $this->assertEquals(0.00, $invoice->balance_due);
        $this->assertEquals('Paid', $invoice->status);
    }

    public function test_tokenized_public_checkout_page_is_accessible()
    {
        $invoice = Invoice::where('balance_due', '>', 0)->first();
        $link = PaymentLink::where('invoice_id', $invoice->id)->where('status', 'Active')->first();

        if (!$link) {
            $link = app(InvoiceService::class)->createPaymentLink($invoice);
        }

        $response = $this->get("/pay/invoice/{$link->token}");
        $response->assertStatus(200);
        $response->assertSee($invoice->invoice_number);
    }

    public function test_webhook_idempotency_prevents_duplicate_payments()
    {
        $invoice = Invoice::where('status', '!=', 'Paid')->first();
        $link = app(InvoiceService::class)->createPaymentLink($invoice);

        $gatewayService = app(PaymentGatewayService::class);
        $eventId = 'evt_test_unique_idempotent_99';

        $payload = [
            'id' => $eventId,
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => 'ch_stripe_test_123',
                    'amount_received' => (int)($invoice->balance_due * 100),
                    'metadata' => [
                        'token' => $link->token,
                    ]
                ]
            ]
        ];

        $request1 = Request::create('/webhooks/stripe', 'POST', $payload);
        $res1 = $gatewayService->processWebhook('stripe', $request1);

        $this->assertTrue($res1['success']);

        // Send exact same webhook event second time
        $request2 = Request::create('/webhooks/stripe', 'POST', $payload);
        $res2 = $gatewayService->processWebhook('stripe', $request2);

        $this->assertTrue($res2['duplicate']);
    }

    public function test_pdf_generation_creates_file()
    {
        $invoice = Invoice::first();
        $pdfService = app(PdfInvoiceService::class);

        $pdfPath = $pdfService->generatePdf($invoice);

        $this->assertFileExists($pdfPath);
    }
}
