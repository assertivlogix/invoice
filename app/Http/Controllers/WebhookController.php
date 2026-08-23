<?php

namespace App\Http\Controllers;

use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    protected PaymentGatewayService $gatewayService;

    public function __construct(PaymentGatewayService $gatewayService)
    {
        $this->gatewayService = $gatewayService;
    }

    public function handle(Request $request, string $gateway)
    {
        $result = $this->gatewayService->processWebhook($gateway, $request);

        if ($result['success'] ?? false) {
            return response()->json($result, 200);
        }

        return response()->json($result, 400);
    }
}
