<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        $gateways = [
            ['name' => 'Stripe', 'slug' => 'stripe', 'is_active' => true, 'is_default' => true, 'keys' => json_encode(['secret_key' => '', 'public_key' => ''])],
            ['name' => 'PayPal', 'slug' => 'paypal', 'is_active' => true, 'is_default' => false, 'keys' => json_encode(['client_id' => '', 'secret' => ''])],
            ['name' => 'Razorpay', 'slug' => 'razorpay', 'is_active' => false, 'is_default' => false, 'keys' => json_encode(['key_id' => '', 'key_secret' => ''])],
            ['name' => 'Paystack', 'slug' => 'paystack', 'is_active' => false, 'is_default' => false, 'keys' => json_encode(['public_key' => '', 'secret_key' => ''])],
            ['name' => 'Flutterwave', 'slug' => 'flutterwave', 'is_active' => false, 'is_default' => false, 'keys' => json_encode(['public_key' => '', 'secret_key' => ''])],
            ['name' => 'Offline Payment', 'slug' => 'offline', 'is_active' => true, 'is_default' => false, 'keys' => json_encode([])],
        ];

        foreach ($gateways as $gateway) {
            PaymentGateway::updateOrCreate(['slug' => $gateway['slug']], $gateway);
        }
    }
}
