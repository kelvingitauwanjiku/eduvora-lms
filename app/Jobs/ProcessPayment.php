<?php

namespace App\Jobs;

use App\Models\PaymentHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $paymentId
    ) {}

    public function handle(): void
    {
        $payment = PaymentHistory::find($this->paymentId);

        if (! $payment || $payment->status !== 'pending') {
            return;
        }

        // Process payment logic here
        $payment->update([
            'status' => 'completed',
        ]);
    }
}
