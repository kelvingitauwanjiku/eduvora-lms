<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\PaymentHistory;
use App\Services\NotificationService;
use App\Services\PaymentGateways\PaymentGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleStripe(Request $request): JsonResponse
    {
        $gateway = PaymentGateway::where('slug', 'stripe')->first();

        if (! $gateway) {
            return response()->json(['error' => 'Gateway not configured'], 404);
        }

        $service = new PaymentGatewayService($gateway);
        $payload = $request->all();
        $signature = $request->header('stripe-signature');

        try {
            $result = $service->handleWebhook($payload, $signature);

            return match ($result['type'] ?? '') {
                'payment_intent.succeeded' => $this->handlePaymentSuccess('stripe', $payload),
                'payment_intent.payment_failed' => $this->handlePaymentFailed('stripe', $payload),
                'charge.refunded' => $this->handleRefund('stripe', $payload),
                default => response()->json(['received' => true]),
            };
        } catch (\Exception $e) {
            Log::error('Stripe webhook error: '.$e->getMessage());

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function handleRazorpay(Request $request): JsonResponse
    {
        $gateway = PaymentGateway::where('slug', 'razorpay')->first();

        if (! $gateway) {
            return response()->json(['error' => 'Gateway not configured'], 404);
        }

        $service = new PaymentGatewayService($gateway);
        $payload = $request->all();

        try {
            $result = $service->handleWebhook($payload, '');

            return match ($result['event'] ?? '') {
                'payment.authorized' => $this->handlePaymentSuccess('razorpay', $payload),
                'payment.failed' => $this->handlePaymentFailed('razorpay', $payload),
                'refund.created' => $this->handleRefund('razorpay', $payload),
                default => response()->json(['received' => true]),
            };
        } catch (\Exception $e) {
            Log::error('Razorpay webhook error: '.$e->getMessage());

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function handlePaystack(Request $request): JsonResponse
    {
        $gateway = PaymentGateway::where('slug', 'paystack')->first();

        if (! $gateway) {
            return response()->json(['error' => 'Gateway not configured'], 404);
        }

        $service = new PaymentGatewayService($gateway);
        $payload = $request->all();

        try {
            $result = $service->handleWebhook($payload, '');

            return match ($result['event'] ?? '') {
                'charge.success' => $this->handlePaymentSuccess('paystack', $payload),
                'charge.failed' => $this->handlePaymentFailed('paystack', $payload),
                'refund.processed' => $this->handleRefund('paystack', $payload),
                default => response()->json(['received' => true]),
            };
        } catch (\Exception $e) {
            Log::error('Paystack webhook error: '.$e->getMessage());

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function handlePaytm(Request $request): JsonResponse
    {
        $gateway = PaymentGateway::where('slug', 'paytm')->first();

        if (! $gateway) {
            return response()->json(['error' => 'Gateway not configured'], 404);
        }

        $payload = $request->all();

        try {
            return match ($payload['STATUS'] ?? '') {
                'TXN_SUCCESS' => $this->handlePaymentSuccess('paytm', $payload),
                'TXN_FAILED' => $this->handlePaymentFailed('paytm', $payload),
                default => response()->json(['received' => true]),
            };
        } catch (\Exception $e) {
            Log::error('Paytm webhook error: '.$e->getMessage());

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function handleSslcommerz(Request $request): JsonResponse
    {
        $gateway = PaymentGateway::where('slug', 'sslcommerz')->first();

        if (! $gateway) {
            return response()->json(['error' => 'Gateway not configured'], 404);
        }

        $payload = $request->all();

        try {
            return match ($payload['status'] ?? '') {
                'VALID' => $this->handlePaymentSuccess('sslcommerz', $payload),
                'FAILED' => $this->handlePaymentFailed('sslcommerz', $payload),
                default => response()->json(['received' => true]),
            };
        } catch (\Exception $e) {
            Log::error('SSLCommerz webhook error: '.$e->getMessage());

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function handlePaypal(Request $request): JsonResponse
    {
        $gateway = PaymentGateway::where('slug', 'paypal')->first();

        if (! $gateway) {
            return response()->json(['error' => 'Gateway not configured'], 404);
        }

        $payload = $request->all();

        try {
            return match ($payload['event_type'] ?? '') {
                'PAYMENT.CAPTURED' => $this->handlePaymentSuccess('paypal', $payload),
                'PAYMENT_DECLINED' => $this->handlePaymentFailed('paypal', $payload),
                'REFUND.CREATED' => $this->handleRefund('paypal', $payload),
                default => response()->json(['received' => true]),
            };
        } catch (\Exception $e) {
            Log::error('PayPal webhook error: '.$e->getMessage());

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    protected function handlePaymentSuccess(string $gateway, array $data): JsonResponse
    {
        $transactionId = $this->extractTransactionId($gateway, $data);

        $payment = PaymentHistory::where('transaction_id', $transactionId)
            ->orWhere('session_id', $transactionId)
            ->first();

        if ($payment && $payment->status !== 'completed') {
            $payment->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $payment->course->increment('students_count');
            $payment->course->user->increment('total_earnings', $payment->instructor_commission);

            $notification = new NotificationService;
            $notification->sendPaymentSuccessNotification(
                $payment->user_id,
                $payment->id,
                $payment->amount
            );
        }

        Log::info("Payment succeeded for gateway {$gateway}: {$transactionId}");

        return response()->json(['received' => true, 'status' => 'processed']);
    }

    protected function handlePaymentFailed(string $gateway, array $data): JsonResponse
    {
        $transactionId = $this->extractTransactionId($gateway, $data);

        $payment = PaymentHistory::where('transaction_id', $transactionId)
            ->orWhere('session_id', $transactionId)
            ->first();

        if ($payment) {
            $payment->update([
                'status' => 'failed',
                'failure_reason' => $this->extractFailureReason($gateway, $data),
            ]);
        }

        Log::warning("Payment failed for gateway {$gateway}: {$transactionId}");

        return response()->json(['received' => true, 'status' => 'processed']);
    }

    protected function handleRefund(string $gateway, array $data): JsonResponse
    {
        $transactionId = $this->extractTransactionId($gateway, $data);

        $payment = PaymentHistory::where('transaction_id', $transactionId)->first();

        if ($payment && $payment->status !== 'refunded') {
            $payment->update([
                'status' => 'refunded',
                'refunded_at' => now(),
            ]);

            $payment->course->decrement('students_count');
            $payment->course->user->decrement('total_earnings', $payment->instructor_commission);
        }

        Log::info("Refund processed for gateway {$gateway}: {$transactionId}");

        return response()->json(['received' => true, 'status' => 'processed']);
    }

    protected function extractTransactionId(string $gateway, array $data): ?string
    {
        return match ($gateway) {
            'stripe' => $data['data']['object']['id'] ?? $data['id'] ?? null,
            'razorpay' => $data['payload']['payment']['entity']['id'] ?? $data['id'] ?? null,
            'paystack' => $data['data']['reference'] ?? $data['reference'] ?? null,
            'paytm' => $data['ORDERID'] ?? $data['orderId'] ?? null,
            'sslcommerz' => $data['tran_id'] ?? $data['transactionId'] ?? null,
            'paypal' => $data['resource']['supplementary_data']['related_ids']['order_id'] ?? $data['id'] ?? null,
            default => null,
        };
    }

    protected function extractFailureReason(string $gateway, array $data): ?string
    {
        return match ($gateway) {
            'stripe' => $data['data']['object']['last_payment_error']['message'] ?? null,
            'razorpay' => $data['payload']['payment']['entity']['error_description'] ?? null,
            'paystack' => $data['data']['gateway_response'] ?? null,
            'paytm' => $data['RESPMSG'] ?? $data['FailReason'] ?? null,
            'sslcommerz' => $data['error_reason'] ?? $data['fail_reason'] ?? null,
            'paypal' => $data['resource']['status_details']['reason'] ?? null,
            default => null,
        };
    }
}
