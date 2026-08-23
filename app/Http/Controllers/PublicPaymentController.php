<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\PaymentLink;
use App\Services\PaymentService;
use App\Services\PdfInvoiceService;
use Illuminate\Http\Request;

class PublicPaymentController extends Controller
{
    protected PaymentService $paymentService;
    protected PdfInvoiceService $pdfService;

    public function __construct(PaymentService $paymentService, PdfInvoiceService $pdfService)
    {
        $this->paymentService = $paymentService;
        $this->pdfService = $pdfService;
    }

    public function showCheckout(string $token)
    {
        $link = PaymentLink::where('token', $token)->first();

        if (!$link || !$link->isValid()) {
            return view('public_payment.expired');
        }

        $invoice = $link->invoice->load(['client', 'items']);
        $company = CompanySetting::getSettings();

        if ($invoice->balance_due <= 0 || $invoice->status === 'Paid') {
            return redirect()->route('public.payment.success', $token);
        }

        $razorpaySetting = \App\Models\PaymentGatewaySetting::where('gateway_name', 'Razorpay')->first();
        $razorpayKey = config('services.razorpay.key') 
            ?: env('RAZORPAY_KEY') 
            ?: ($razorpaySetting && $razorpaySetting->api_key ? $razorpaySetting->api_key : 'rzp_test_SxtSt6k8BprpdE');

        return view('public_payment.checkout', compact('link', 'invoice', 'company', 'razorpayKey'));
    }

    public function processPayment(Request $request, string $token)
    {
        $link = PaymentLink::where('token', $token)->first();

        if (!$link || !$link->isValid()) {
            return redirect()->route('public.payment.failed', $token)->with('error', 'Payment link expired or invalid.');
        }

        $invoice = $link->invoice;
        $gatewayMethod = 'Razorpay';
        $txnId = $request->input('razorpay_payment_id') ?: ('pay_rzp_' . strtoupper(\Illuminate\Support\Str::random(12)));

        // Record payment
        $payment = $this->paymentService->recordPayment([
            'invoice_id' => $invoice->id,
            'amount' => $invoice->balance_due,
            'payment_method' => $gatewayMethod,
            'transaction_id' => $txnId,
            'notes' => 'Razorpay online checkout payment by client',
        ]);

        return redirect()->route('public.payment.success', $token);
    }

    public function success(string $token)
    {
        $link = PaymentLink::where('token', $token)->firstOrFail();
        $invoice = $link->invoice->load(['client', 'payments']);
        $company = CompanySetting::getSettings();
        $latestPayment = $invoice->payments()->latest()->first();

        return view('public_payment.success', compact('link', 'invoice', 'company', 'latestPayment'));
    }

    public function failed(string $token)
    {
        $link = PaymentLink::where('token', $token)->first();
        $invoice = $link ? $link->invoice : null;
        $company = CompanySetting::getSettings();

        return view('public_payment.failed', compact('link', 'invoice', 'company'));
    }

    public function downloadPdf(string $token)
    {
        $link = PaymentLink::where('token', $token)->firstOrFail();
        $invoice = $link->invoice;

        $pdfPath = $this->pdfService->generatePdf($invoice);
        return response()->download($pdfPath, basename($pdfPath));
    }
}
