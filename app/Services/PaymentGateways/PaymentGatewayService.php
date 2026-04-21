<?php

namespace App\Services\PaymentGateways;

use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentGatewayService
{
    protected PaymentGateway $gateway;
    protected array $config;

    public function __construct(PaymentGateway $gateway)
    {
        $this->gateway = $gateway;
        $this->config = $this->resolveConfig();
    }

    protected function resolveConfig(): array
    {
        $keys = $this->gateway->keys ?? [];
        
        if ($this->gateway->test_mode) {
            return $keys['test'] ?? $keys;
        }

        return $keys['live'] ?? $keys;
    }

    public function getGateway(): PaymentGateway
    {
        return $this->gateway;
    }

    public function isActive(): bool
    {
        return $this->gateway->is_active;
    }

    public function getName(): string
    {
        return $this->gateway->name;
    }

    public function getSlug(): string
    {
        return $this->gateway->slug;
    }

    public function createPaymentIntent(float $amount, string $currency = 'USD', array $metadata = []): array
    {
        return match ($this->gateway->slug) {
            'stripe' => $this->createStripePaymentIntent($amount, $currency, $metadata),
            'razorpay' => $this->createRazorpayOrder($amount, $currency, $metadata),
            'paystack' => $this->createPaystackTransaction($amount, $currency, $metadata),
            'paytm' => $this->createPaytmTransaction($amount, $currency, $metadata),
            'sslcommerz' => $this->createSslcommerzPayment($amount, $currency, $metadata),
            'paypal' => $this->createPaypalOrder($amount, $currency, $metadata),
            default => throw new \Exception("Gateway {$this->gateway->slug} not supported"),
        };
    }

    public function verifyPayment(string $transactionId): array
    {
        return match ($this->gateway->slug) {
            'stripe' => $this->verifyStripePayment($transactionId),
            'razorpay' => $this->verifyRazorpayPayment($transactionId),
            'paystack' => $this->verifyPaystackPayment($transactionId),
            'paytm' => $this->verifyPaytmPayment($transactionId),
            'sslcommerz' => $this->verifySslcommerzPayment($transactionId),
            'paypal' => $this->verifyPaypalPayment($transactionId),
            default => throw new \Exception("Gateway {$this->gateway->slug} not supported"),
        };
    }

    public function handleWebhook(array $payload, string $signature): array
    {
        return match ($this->gateway->slug) {
            'stripe' => $this->handleStripeWebhook($payload, $signature),
            'razorpay' => $this->handleRazorpayWebhook($payload),
            'paystack' => $this->handlePaystackWebhook($payload),
            'paytm' => $this->handlePaytmWebhook($payload),
            'sslcommerz' => $this->handleSslcommerzWebhook($payload),
            'paypal' => $this->handlePaypalWebhook($payload),
            default => throw new \Exception("Gateway {$this->gateway->slug} not supported"),
        };
    }

    public function processRefund(string $transactionId, float $amount): array
    {
        return match ($this->gateway->slug) {
            'stripe' => $this->stripeRefund($transactionId, $amount),
            'razorpay' => $this->razorpayRefund($transactionId, $amount),
            'paystack' => $this->paystackRefund($transactionId, $amount),
            'paytm' => $this->paytmRefund($transactionId, $amount),
            'sslcommerz' => $this->sslcommerzRefund($transactionId, $amount),
            'paypal' => $this->paypalRefund($transactionId, $amount),
            default => throw new \Exception("Gateway {$this->gateway->slug} not supported"),
        };
    }

    protected function createStripePaymentIntent(float $amount, string $currency, array $metadata): array
    {
        $response = Http::withToken($this->config['secret_key'])
            ->post('https://api.stripe.com/v1/payment_intents', [
                'amount' => (int) ($amount * 100),
                'currency' => strtolower($currency),
                'metadata' => $metadata,
            ]);

        if ($response->failed()) {
            throw new \Exception('Stripe payment intent creation failed: ' . $response->body());
        }

        $data = $response->json();
        return [
            'id' => $data['id'],
            'client_secret' => $data['client_secret'],
            'status' => $data['status'],
        ];
    }

    protected function verifyStripePayment(string $transactionId): array
    {
        $response = Http::withToken($this->config['secret_key'])
            ->get("https://api.stripe.com/v1/payment_intents/{$transactionId}");

        if ($response->failed()) {
            return ['status' => 'failed'];
        }

        return [
            'status' => $response->json('status'),
            'amount' => $response->json('amount') / 100,
        ];
    }

    protected function handleStripeWebhook(array $payload, string $signature): array
    {
        $webhookSecret = $this->config['webhook_secret'] ?? null;
        
        return [
            'type' => $payload['type'] ?? null,
            'data' => $payload['data']['object'] ?? $payload,
        ];
    }

    protected function stripeRefund(string $transactionId, float $amount): array
    {
        $response = Http::withToken($this->config['secret_key'])
            ->post('https://api.stripe.com/v1/refunds', [
                'payment_intent' => $transactionId,
                'amount' => (int) ($amount * 100),
            ]);

        if ($response->failed()) {
            throw new \Exception('Stripe refund failed: ' . $response->body());
        }

        return [
            'id' => $response->json('id'),
            'status' => $response->json('status'),
        ];
    }

    protected function createRazorpayOrder(float $amount, string $currency, array $metadata): array
    {
        $response = Http::withBasicAuth($this->config['key_id'], $this->config['key_secret'])
            ->post('https://api.razorpay.com/v1/orders', [
                'amount' => (int) ($amount * 100),
                'currency' => $currency,
                'receipt' => $metadata['order_number'] ?? 'ord_' . Str::random(10),
                'metadata' => $metadata,
            ]);

        if ($response->failed()) {
            throw new \Exception('Razorpay order creation failed: ' . $response->body());
        }

        $data = $response->json();
        return [
            'id' => $data['id'],
            'amount' => $data['amount'],
            'currency' => $data['currency'],
        ];
    }

    protected function verifyRazorpayPayment(string $transactionId): array
    {
        $response = Http::withBasicAuth($this->config['key_id'], $this->config['key_secret'])
            ->get("https://api.razorpay.com/v1/payments/{$transactionId}");

        if ($response->failed()) {
            return ['status' => 'failed'];
        }

        return [
            'status' => $response->json('status'),
            'amount' => $response->json('amount') / 100,
        ];
    }

    protected function handleRazorpayWebhook(array $payload): array
    {
        return [
            'event' => $payload['event'] ?? null,
            'payment' => $payload['payload']['payment']['entity'] ?? $payload,
        ];
    }

    protected function razorpayRefund(string $transactionId, float $amount): array
    {
        $response = Http::withBasicAuth($this->config['key_id'], $this->config['key_secret'])
            ->post('https://api.razorpay.com/v1/refunds', [
                'payment_id' => $transactionId,
                'amount' => (int) ($amount * 100),
            ]);

        if ($response->failed()) {
            throw new \Exception('Razorpay refund failed: ' . $response->body());
        }

        return [
            'id' => $response->json('id'),
            'status' => $response->json('status'),
        ];
    }

    protected function createPaystackTransaction(float $amount, string $currency, array $metadata): array
    {
        $response = Http::withToken($this->config['secret_key'])
            ->post('https://api.paystack.co/transaction/initialize', [
                'amount' => (int) ($amount * 100),
                'currency' => $currency,
                'reference' => 'pay_' . Str::random(16),
                'metadata' => $metadata,
            ]);

        if ($response->failed()) {
            throw new \Exception('Paystack transaction initialization failed: ' . $response->body());
        }

        $data = $response->json();
        return [
            'reference' => $data['data']['reference'],
            'authorization_url' => $data['data']['authorization_url'],
        ];
    }

    protected function verifyPaystackPayment(string $transactionId): array
    {
        $response = Http::withToken($this->config['secret_key'])
            ->get("https://api.paystack.co/transaction/verify/{$transactionId}");

        if ($response->failed()) {
            return ['status' => 'failed'];
        }

        $data = $response->json('data');
        return [
            'status' => $data['status'],
            'amount' => $data['amount'] / 100,
        ];
    }

    protected function handlePaystackWebhook(array $payload): array
    {
        return [
            'event' => $payload['event'] ?? null,
            'data' => $payload['data'] ?? $payload,
        ];
    }

    protected function paystackRefund(string $transactionId, float $amount): array
    {
        $response = Http::withToken($this->config['secret_key'])
            ->post('https://api.paystack.co/refund', [
                'transaction' => $transactionId,
                'amount' => (int) ($amount * 100),
            ]);

        if ($response->failed()) {
            throw new \Exception('Paystack refund failed: ' . $response->body());
        }

        return [
            'id' => $response->json('data.id'),
            'status' => $response->json('data.status'),
        ];
    }

    protected function createPaytmTransaction(float $amount, string $currency, array $metadata): array
    {
        $orderId = $metadata['order_number'] ?? 'ord_' . Str::random(10);
        
        $checksum = $this->generatePaytmChecksum([
            'MID' => $this->config['merchant_id'],
            'ORDERID' => $orderId,
            'TXNAMOUNT' => $amount,
            'CURRENCY' => $currency,
        ], $this->config['merchant_key']);

        $response = Http::post('https://securegw.paytm.in/theia/api/v1/initiateTransaction', [
            'MID' => $this->config['merchant_id'],
            'ORDERID' => $orderId,
            'TXNAMOUNT' => $amount,
            'CURRENCY' => $currency,
            'CHANNEL_ID' => 'WEB',
            ' INDUSTRY_TYPE_ID' => 'Retail',
            'WEBSITE' => $this->config['website'],
            'CHECKSUMHASH' => $checksum,
            'CALLBACK_URL' => $this->gateway->redirect_url,
        ]);

        if ($response->failed()) {
            throw new \Exception('Paytm transaction creation failed: ' . $response->body());
        }

        $data = $response->json();
        return [
            'order_id' => $orderId,
            'txn_token' => $data['txnToken'] ?? null,
        ];
    }

    protected function verifyPaytmPayment(string $transactionId): array
    {
        $response = Http::post('https://securegw.paytm.in/v3/order/status', [
            'MID' => $this->config['merchant_id'],
            'ORDERID' => $transactionId,
            'CHECKSUMHASH' => $this->generatePaytmChecksum([
                'MID' => $this->config['merchant_id'],
                'ORDERID' => $transactionId,
            ], $this->config['merchant_key']),
        ]);

        if ($response->failed()) {
            return ['status' => 'failed'];
        }

        $data = $response->json();
        return [
            'status' => $data['resultInfo']['resultCode'] ?? 'failed',
            'amount' => $data['txnAmount'] ?? 0,
        ];
    }

    protected function handlePaytmWebhook(array $payload): array
    {
        return $payload;
    }

    protected function paytmRefund(string $transactionId, float $amount): array
    {
        return ['status' => 'pending'];
    }

    protected function generatePaytmChecksum(array $params, string $key): string
    {
        ksort($params);
        $paramStr = implode('&', array_map(fn($k, $v) => "{$k}={$v}", array_keys($params), $params));
        return hash_hmac('sha256', $paramStr, $key);
    }

    protected function createSslcommerzPayment(float $amount, string $currency, array $metadata): array
    {
        $response = Http::post('https://sandbox.sslcommerz.com/gwprocess/v4/api.php', [
            'store_id' => $this->config['store_id'],
            'store_passwd' => $this->config['store_password'],
            'total_amount' => $amount,
            'currency' => $currency,
            'tran_id' => $metadata['order_number'] ?? 'ord_' . Str::random(10),
            'success_url' => $this->gateway->redirect_url,
            'fail_url' => $this->gateway->redirect_url,
            'cancel_url' => $this->gateway->redirect_url,
            'ipn_url' => $this->gateway->webhook_url,
            'emi_option' => 0,
            'product_name' => $metadata['description'] ?? 'Course Purchase',
            'product_category' => 'Digital Education',
        ]);

        if ($response->failed()) {
            throw new \Exception('SSLCommerz payment creation failed: ' . $response->body());
        }

        $data = $response->json();
        return [
            'session_key' => $data['sessionkey'],
            'redirect_url' => $data['GatewayPageURL'],
        ];
    }

    protected function verifySslcommerzPayment(string $transactionId): array
    {
        $response = Http::post('https://sandbox.sslcommerz.com/validator/api/merchantTransIDValidationAPI.php', [
            'store_id' => $this->config['store_id'],
            'store_passwd' => $this->config['store_password'],
            'tran_id' => $transactionId,
            'verify_logs' => 1,
        ]);

        if ($response->failed()) {
            return ['status' => 'failed'];
        }

        return [
            'status' => $response->json('status'),
            'amount' => $response->json('amount'),
        ];
    }

    protected function handleSslcommerzWebhook(array $payload): array
    {
        return $payload;
    }

    protected function sslcommerzRefund(string $transactionId, float $amount): array
    {
        return ['status' => 'pending'];
    }

    protected function createPaypalOrder(float $amount, string $currency, array $metadata): array
    {
        $response = Http::withToken($this->config['access_token'])
            ->post('https://api-m.sandbox.paypal.com/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => $metadata['order_number'] ?? 'ord_' . Str::random(10),
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => number_format($amount, 2, '.', ''),
                    ],
                    'description' => $metadata['description'] ?? 'Course Purchase',
                ]],
            ]);

        if ($response->failed()) {
            throw new \Exception('PayPal order creation failed: ' . $response->body());
        }

        $data = $response->json();
        return [
            'id' => $data['id'],
            'status' => $data['status'],
            'links' => collect($data['links'])->pluck('href', 'rel')->toArray(),
        ];
    }

    protected function verifyPaypalPayment(string $transactionId): array
    {
        $response = Http::withToken($this->config['access_token'])
            ->get("https://api-m.sandbox.paypal.com/v2/checkout/orders/{$transactionId}");

        if ($response->failed()) {
            return ['status' => 'failed'];
        }

        $data = $response->json();
        return [
            'status' => $data['status'],
            'purchase_units' => $data['purchase_units'],
        ];
    }

    protected function handlePaypalWebhook(array $payload): array
    {
        return [
            'event_type' => $payload['event_type'] ?? null,
            'resource' => $payload['resource'] ?? $payload,
        ];
    }

    protected function paypalRefund(string $transactionId, float $amount): array
    {
        $response = Http::withToken($this->config['access_token'])
            ->post("https://api-m.sandbox.paypal.com/v2/payments/captures/{$transactionId}/refund", [
                'amount' => [
                    'value' => number_format($amount, 2, '.', ''),
                    'currency_code' => 'USD',
                ],
            ]);

        if ($response->failed()) {
            throw new \Exception('PayPal refund failed: ' . $response->body());
        }

        return [
            'id' => $response->json('id'),
            'status' => $response->json('status'),
        ];
    }

    public static function calculateFee(float $amount, ?PaymentGateway $gateway = null): array
    {
        if (!$gateway) {
            return [
                'fee' => 0,
                'net_amount' => $amount,
            ];
        }

        return $gateway->calculateFee($amount);
    }
}