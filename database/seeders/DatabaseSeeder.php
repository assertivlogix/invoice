<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\CompanySetting;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceTax;
use App\Models\Payment;
use App\Models\PaymentGatewaySetting;
use App\Models\PaymentLink;
use App\Models\Project;
use App\Models\Service;
use App\Models\Tax;
use App\Models\User;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Users
        $admin = User::firstOrCreate(['email' => 'admin@assertivlogix.com'], [
            'name' => 'Administrator',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+1 (555) 019-2834',
            'status' => 'active',
        ]);

        $staff = User::firstOrCreate(['email' => 'staff@assertivlogix.com'], [
            'name' => 'John Staff',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'phone' => '+1 (555) 019-8800',
            'status' => 'active',
        ]);

        // 2. Company Settings
        CompanySetting::getSettings();

        // 3. Currencies
        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 1.000000, 'is_default' => true],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'exchange_rate' => 0.920000, 'is_default' => false],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'exchange_rate' => 0.780000, 'is_default' => false],
            ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹', 'exchange_rate' => 83.500000, 'is_default' => false],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'CA$', 'exchange_rate' => 1.360000, 'is_default' => false],
            ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'A$', 'exchange_rate' => 1.520000, 'is_default' => false],
            ['code' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'AED', 'exchange_rate' => 3.670000, 'is_default' => false],
        ];
        foreach ($currencies as $curr) {
            Currency::firstOrCreate(['code' => $curr['code']], $curr);
        }

        // 4. Taxes
        $taxes = [
            ['name' => 'GST 18%', 'rate' => 18.00, 'type' => 'GST'],
            ['name' => 'CGST 9%', 'rate' => 9.00, 'type' => 'CGST'],
            ['name' => 'SGST 9%', 'rate' => 9.00, 'type' => 'SGST'],
            ['name' => 'IGST 18%', 'rate' => 18.00, 'type' => 'IGST'],
            ['name' => 'VAT 20%', 'rate' => 20.00, 'type' => 'VAT'],
        ];
        foreach ($taxes as $tax) {
            Tax::firstOrCreate(['name' => $tax['name']], $tax);
        }

        // 5. Gateways
        PaymentGatewaySetting::firstOrCreate(['gateway_name' => 'Stripe'], [
            'api_key' => 'pk_test_51NxExampleKeyStripe',
            'secret_key' => 'sk_test_51NxExampleSecretStripe',
            'webhook_secret' => 'whsec_ExampleStripe',
            'environment' => 'test',
            'currency' => 'USD',
            'is_active' => true,
        ]);
        PaymentGatewaySetting::firstOrCreate(['gateway_name' => 'Razorpay'], [
            'api_key' => 'rzp_test_ExampleKeyRazorpay',
            'secret_key' => 'rzp_secret_ExampleSecretRazorpay',
            'webhook_secret' => 'rzp_wh_Example',
            'environment' => 'test',
            'currency' => 'INR',
            'is_active' => true,
        ]);

        // 6. Demo Services
        $servicesData = [
            ['service_name' => 'WordPress Development', 'sku' => 'SRV-WP-01', 'unit' => 'Hour', 'default_price' => 50.00, 'tax_rate' => 0.00],
            ['service_name' => 'WooCommerce Development', 'sku' => 'SRV-WOO-01', 'unit' => 'Hour', 'default_price' => 65.00, 'tax_rate' => 0.00],
            ['service_name' => 'Laravel Custom Application', 'sku' => 'SRV-LAR-01', 'unit' => 'Hour', 'default_price' => 85.00, 'tax_rate' => 0.00],
            ['service_name' => 'Website UI/UX Design', 'sku' => 'SRV-DES-01', 'unit' => 'Project', 'default_price' => 1500.00, 'tax_rate' => 0.00],
            ['service_name' => 'Custom Plugin Development', 'sku' => 'SRV-PLG-01', 'unit' => 'Project', 'default_price' => 2000.00, 'tax_rate' => 0.00],
            ['service_name' => 'WordPress Maintenance Retainer', 'sku' => 'SRV-MNT-01', 'unit' => 'Month', 'default_price' => 500.00, 'tax_rate' => 0.00],
            ['service_name' => 'SEO Audit & Optimization', 'sku' => 'SRV-SEO-01', 'unit' => 'Month', 'default_price' => 800.00, 'tax_rate' => 0.00],
            ['service_name' => 'Mobile App Development', 'sku' => 'SRV-MOB-01', 'unit' => 'Hour', 'default_price' => 95.00, 'tax_rate' => 0.00],
            ['service_name' => 'Page Speed Optimization', 'sku' => 'SRV-SPD-01', 'unit' => 'Project', 'default_price' => 750.00, 'tax_rate' => 0.00],
            ['service_name' => 'API Integration & Webhooks', 'sku' => 'SRV-API-01', 'unit' => 'Hour', 'default_price' => 75.00, 'tax_rate' => 0.00],
        ];

        $services = [];
        foreach ($servicesData as $sData) {
            $services[] = Service::firstOrCreate(['sku' => $sData['sku']], $sData);
        }

        // 7. Demo Clients
        $clientsData = [
            [
                'client_id' => 'CLI-001',
                'client_type' => 'company',
                'company_name' => 'ABC Technologies',
                'contact_person' => 'Robert Johnson',
                'email' => 'robert@abctechnologies.com',
                'phone' => '+1 (415) 555-0142',
                'website' => 'https://abctechnologies.com',
                'address' => '500 Market Street, Suite 800',
                'city' => 'San Francisco',
                'state' => 'CA',
                'country' => 'United States',
                'zip_code' => '94105',
                'currency' => 'USD',
                'payment_terms' => 'Net 30',
                'status' => 'active',
            ],
            [
                'client_id' => 'CLI-002',
                'client_type' => 'company',
                'company_name' => 'XYZ Solutions Ltd',
                'contact_person' => 'Sarah Connor',
                'email' => 'sarah@xyzsolutions.co.uk',
                'phone' => '+44 20 7946 0912',
                'website' => 'https://xyzsolutions.co.uk',
                'address' => '12 Bishopgate St',
                'city' => 'London',
                'state' => 'Greater London',
                'country' => 'United Kingdom',
                'zip_code' => 'EC2N 4AG',
                'currency' => 'GBP',
                'payment_terms' => 'Net 15',
                'status' => 'active',
            ],
            [
                'client_id' => 'CLI-003',
                'client_type' => 'company',
                'company_name' => 'Global Web Agency',
                'contact_person' => 'Vikram Patel',
                'email' => 'vikram@globalweb.in',
                'phone' => '+91 22 2490 1234',
                'website' => 'https://globalweb.in',
                'address' => 'B-302 Lotus Corporate Park, Goregaon',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'country' => 'India',
                'zip_code' => '400063',
                'currency' => 'INR',
                'payment_terms' => 'Net 30',
                'status' => 'active',
            ],
            [
                'client_id' => 'CLI-004',
                'client_type' => 'company',
                'company_name' => 'Demo Corporation',
                'contact_person' => 'Michael Chang',
                'email' => 'michael@democorp.com',
                'phone' => '+1 (212) 555-0199',
                'website' => 'https://democorp.com',
                'address' => '350 Fifth Ave, Floor 42',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'United States',
                'zip_code' => '10118',
                'currency' => 'USD',
                'payment_terms' => 'Net 30',
                'status' => 'active',
            ],
            [
                'client_id' => 'CLI-005',
                'client_type' => 'company',
                'company_name' => 'Digital Marketing Ltd',
                'contact_person' => 'Emma Watson',
                'email' => 'emma@digitalmarketing.com.au',
                'phone' => '+61 2 9250 0000',
                'website' => 'https://digitalmarketing.com.au',
                'address' => '88 George Street',
                'city' => 'Sydney',
                'state' => 'NSW',
                'country' => 'Australia',
                'zip_code' => '2000',
                'currency' => 'AUD',
                'payment_terms' => 'Due on Receipt',
                'status' => 'active',
            ],
        ];

        $clients = [];
        foreach ($clientsData as $cData) {
            $clients[] = Client::firstOrCreate(['client_id' => $cData['client_id']], $cData);
        }

        // 8. Demo Projects
        $projectsData = [
            ['project_id' => 'PRJ-001', 'client_id' => $clients[0]->id, 'project_name' => 'Corporate Website Redesign', 'budget' => 5000.00, 'status' => 'In Progress'],
            ['project_id' => 'PRJ-002', 'client_id' => $clients[0]->id, 'project_name' => 'WooCommerce Portal Development', 'budget' => 8500.00, 'status' => 'In Progress'],
            ['project_id' => 'PRJ-003', 'client_id' => $clients[1]->id, 'project_name' => 'Custom SaaS Platform', 'budget' => 12000.00, 'status' => 'In Progress'],
            ['project_id' => 'PRJ-004', 'client_id' => $clients[2]->id, 'project_name' => 'E-Commerce Mobile Application', 'budget' => 6000.00, 'status' => 'Completed'],
            ['project_id' => 'PRJ-005', 'client_id' => $clients[3]->id, 'project_name' => 'Website Monthly Retainer', 'budget' => 2500.00, 'status' => 'In Progress'],
            ['project_id' => 'PRJ-006', 'client_id' => $clients[4]->id, 'project_name' => 'SEO & Speed Optimization', 'budget' => 1800.00, 'status' => 'Completed'],
        ];

        $projects = [];
        foreach ($projectsData as $pData) {
            $projects[] = Project::firstOrCreate(['project_id' => $pData['project_id']], $pData);
        }

        // 9. Generate Realistic Demo Invoices & Payments
        $invoiceService = app(InvoiceService::class);
        $paymentService = app(PaymentService::class);

        // Invoice 1: ABC Technologies - Paid
        $inv1 = $invoiceService->createInvoice([
            'client_id' => $clients[0]->id,
            'project_id' => $projects[0]->id,
            'invoice_number' => 'INV-2026-0001',
            'invoice_date' => now()->subMonths(2)->format('Y-m-d'),
            'due_date' => now()->subMonths(1)->format('Y-m-d'),
            'currency' => 'USD',
            'payment_terms' => 'Net 30',
            'template' => 'modern',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'status' => 'Sent',
        ], [
            ['item_name' => 'WordPress Development', 'service_id' => $services[0]->id, 'quantity' => 40, 'unit' => 'Hour', 'unit_price' => 50.00],
            ['item_name' => 'Website UI/UX Design', 'service_id' => $services[3]->id, 'quantity' => 1, 'unit' => 'Project', 'unit_price' => 1500.00],
        ]);

        $paymentService->recordPayment([
            'invoice_id' => $inv1->id,
            'amount' => $inv1->grand_total,
            'payment_method' => 'Stripe',
            'transaction_id' => 'ch_3NxStripeDemo001',
            'payment_date' => now()->subMonths(1)->subDays(5)->format('Y-m-d'),
        ]);

        // Invoice 2: ABC Technologies - Partially Paid
        $inv2 = $invoiceService->createInvoice([
            'client_id' => $clients[0]->id,
            'project_id' => $projects[1]->id,
            'invoice_number' => 'INV-2026-0002',
            'invoice_date' => now()->subDays(20)->format('Y-m-d'),
            'due_date' => now()->addDays(10)->format('Y-m-d'),
            'currency' => 'USD',
            'payment_terms' => 'Net 30',
            'template' => 'modern',
            'discount_type' => 'percentage',
            'discount_value' => 10.00,
            'status' => 'Sent',
        ], [
            ['item_name' => 'WooCommerce Development', 'service_id' => $services[1]->id, 'quantity' => 60, 'unit' => 'Hour', 'unit_price' => 65.00],
            ['item_name' => 'Custom Plugin Development', 'service_id' => $services[4]->id, 'quantity' => 1, 'unit' => 'Project', 'unit_price' => 2000.00],
        ]);

        $paymentService->recordPayment([
            'invoice_id' => $inv2->id,
            'amount' => 2000.00,
            'payment_method' => 'Bank Transfer',
            'transaction_id' => 'WIRE-2026-8810',
            'payment_date' => now()->subDays(10)->format('Y-m-d'),
        ]);

        // Invoice 3: XYZ Solutions - Overdue
        $inv3 = $invoiceService->createInvoice([
            'client_id' => $clients[1]->id,
            'project_id' => $projects[2]->id,
            'invoice_number' => 'INV-2026-0003',
            'invoice_date' => now()->subDays(45)->format('Y-m-d'),
            'due_date' => now()->subDays(15)->format('Y-m-d'),
            'currency' => 'GBP',
            'payment_terms' => 'Net 30',
            'template' => 'corporate',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'status' => 'Sent',
        ], [
            ['item_name' => 'Laravel Custom Application', 'service_id' => $services[2]->id, 'quantity' => 50, 'unit' => 'Hour', 'unit_price' => 85.00],
        ]);

        // Invoice 4: Global Web Agency - Sent
        $inv4 = $invoiceService->createInvoice([
            'client_id' => $clients[2]->id,
            'project_id' => $projects[3]->id,
            'invoice_number' => 'INV-2026-0004',
            'invoice_date' => now()->subDays(5)->format('Y-m-d'),
            'due_date' => now()->addDays(25)->format('Y-m-d'),
            'currency' => 'INR',
            'payment_terms' => 'Net 30',
            'template' => 'modern',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'status' => 'Sent',
        ], [
            ['item_name' => 'Mobile App Development', 'service_id' => $services[7]->id, 'quantity' => 40, 'unit' => 'Hour', 'unit_price' => 95.00],
        ]);

        // Invoice 5: Demo Corporation - Draft
        $inv5 = $invoiceService->createInvoice([
            'client_id' => $clients[3]->id,
            'project_id' => $projects[4]->id,
            'invoice_number' => 'INV-2026-0005',
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'currency' => 'USD',
            'payment_terms' => 'Net 30',
            'template' => 'modern',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'status' => 'Draft',
        ], [
            ['item_name' => 'WordPress Maintenance Retainer', 'service_id' => $services[5]->id, 'quantity' => 1, 'unit' => 'Month', 'unit_price' => 500.00],
            ['item_name' => 'Page Speed Optimization', 'service_id' => $services[8]->id, 'quantity' => 1, 'unit' => 'Project', 'unit_price' => 750.00],
        ]);

        $invoiceService->updateOverdueStatuses();
    }
}
