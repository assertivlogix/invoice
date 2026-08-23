<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\PaymentGatewaySetting;
use App\Models\PaymentLink;
use App\Models\PaymentTransaction;
use App\Models\PaymentWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentGatewayService
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function processWebhook(string $gateway, Request $request): array
    {
        $payload = $request->all();
        $signature = $request->header('Stripe-Signature') ?? $request->header('X-Razorpay-Signature') ?? $request->header('PAYPAL-TRANSACTION-SIGNATURE');

        // Extract event_id based on gateway
        $eventId = match(strtolower($gateway)) {
            'stripe' => $payload['id'] ?? null,
            'razorpay' => $payload['event_id'] ?? ($payload['payload']['payment']['entity']['id'] ?? null),
            'paypal' => $payload['id'] ?? null,
            default => null,
        };

        if (!$eventId) {
            $eventId = 'evt_' . md5(json_encode($payload));
        }

        // Idempotency check: prevent processing exact same webhook event twice
        $existing = PaymentWebhook::where('event_id', $eventId)->first();
        if ($existing && $existing->processed) {
            return [
                'success' => true,
                'message' => 'Webhook already processed (Idempotent call)',
                'duplicate' => true,
            ];
        }

        $webhook = PaymentWebhook::create([
            'gateway' => ucfirst($gateway),
            'event_id' => $eventId,
            'event_type' => $payload['type'] ?? $payload['event'] ?? 'payment.succeeded',
            'payload' => $payload,
            'signature' => $signature,
            'processed' => false,
        ]);

        // Process webhook event
        try {
            DB::transaction(function () use ($gateway, $payload, $webhook) {
                $token = $payload['data']['object']['metadata']['token'] ?? $payload['token'] ?? $payload['payload']['payment']['entity']['notes']['token'] ?? null;
                $amount = (float) ($payload['data']['object']['amount_received'] ?? $payload['payload']['payment']['entity']['amount'] ?? $payload['resource']['amount']['value'] ?? 0);
                
                if (strtolower($gateway) === 'stripe' || strtolower($gateway) === 'razorpay') {
                    if (str_contains(json_encode($payload), 'amount') && $amount > 100) {
                        // Stripe & Razorpay use smallest currency unit (cents/paise)
                        $amount = $amount / 100;
                    }
                }

                $link = null;
                if ($token) {
                    $link = PaymentLink::where('token', $token)->first();
                }

                if ($link) {
                    $invoice = $link->invoice;
                    $txnId = $payload['data']['object']['id'] ?? $payload['payload']['payment']['entity']['id'] ?? $payload['resource']['id'] ?? 'TXN_' . time();

                    // Record Payment Transaction
                    PaymentTransaction::create([
                        'invoice_id' => $invoice->id,
                        'client_id' => $invoice->client_id,
                        'gateway' => ucfirst($gateway),
                        'gateway_transaction_id' => $txnId,
                        'amount' => $amount > 0 ? $amount : $link->amount,
                        'currency' => $link->currency,
                        'status' => 'Completed',
                        'payment_method' => ucfirst($gateway),
                        'transaction_date' => now(),
                        'raw_response' => $payload,
                    ]);

                    // Automatically record payment on invoice
                    $payment = $this->paymentService->recordPayment([
                        'invoice_id' => $invoice->id,
                        'amount' => $amount > 0 ? $amount : $link->amount,
                        'payment_method' => ucfirst($gateway),
                        'transaction_id' => $txnId,
                        'notes' => "Automated online payment via " . ucfirst($gateway),
                    ]);

                    $webhook->update([
                        'payment_id' => $payment->id,
                        'processed' => true,
                        'processed_at' => now(),
                    ]);
                } else {
                    $webhook->update([
                        'processed' => true,
                        'processed_at' => now(),
                        'error_message' => 'No matching payment link token found in payload',
                    ]);
                }
            });

            return ['success' => true, 'message' => 'Webhook processed successfully'];
        } catch (\Exception $e) {
            $webhook->update([
                'processed' => false,
                'error_message' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
