<?php

namespace App\Listeners;

use App\Events\SuspiciousActivity;
use Illuminate\Support\Facades\Log;

class LogSuspiciousActivity
{
    public function handle(SuspiciousActivity $event): void
    {
        Log::warning('Suspicious activity detected', [
            'user_id' => $event->user->id,
            'type' => $event->data['type'] ?? 'unknown',
            'ip' => $event->data['ip'] ?? request()->ip(),
            'device_id' => $event->data['device_id'] ?? null,
            'details' => $event->data,
        ]);
    }
}