<?php

namespace App\Jobs;

use App\Models\PaymentHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPaymentWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $gateway,
        public array $payload,
        public string $transactionId
    ) {}

    public function handle(): void
    {
        Log::info("Processing webhook for gateway: {$this->gateway}");

        $payment = PaymentHistory::where('transaction_id', $this->transactionId)
            ->orWhere('session_id', $this->transactionId)
            ->first();

        if (! $payment) {
            Log::warning("Payment not found for transaction: {$this->transactionId}");

            return;
        }

        $status = $this->determineStatus();

        if ($payment->status !== $status) {
            $payment->update([
                'status' => $status,
                'completed_at' => $status === 'completed' ? now() : null,
            ]);

            if ($status === 'completed') {
                $payment->course->increment('students_count');
                $payment->course->user->increment('total_earnings', $payment->instructor_commission);
            }
        }

        Log::info("Webhook processed for payment: {$payment->id}");
    }

    protected function determineStatus(): string
    {
        return match ($this->gateway) {
            'stripe' => $this->payload['data']['object']['status'] ?? 'pending',
            'razorpay' => $this->payload['status'] ?? 'pending',
            'paystack' => $this->payload['status'] ?? 'pending',
            default => 'pending',
        };
    }
}
