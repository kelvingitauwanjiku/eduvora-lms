<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentHistory;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PayoutController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $payouts = Payout::with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($payouts);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:bank_transfer,paypal,razorpay,stripe',
            'bank_details' => 'required_if:payment_method,bank_transfer|array',
            'bank_details.account_name' => 'required_with:bank_details|string',
            'bank_details.account_number' => 'required_with:bank_details|string',
            'bank_details.bank_name' => 'required_with:bank_details|string',
            'bank_details.swift_code' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $user = User::findOrFail($request->user_id);

        $availableBalance = $this->getAvailableBalance($user->id);

        if ($request->amount > $availableBalance) {
            return response()->json([
                'error' => 'Insufficient balance',
                'available' => $availableBalance,
                'requested' => $request->amount,
            ], 400);
        }

        $payout = Payout::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'currency' => 'USD',
            'payment_method' => $request->payment_method,
            'bank_details' => $request->bank_details ?? [],
            'status' => 'pending',
            'notes' => $request->notes,
            'created_by' => $request->user()->id,
        ]);

        Log::info("Payout created: {$payout->id} for user {$user->id}");

        return response()->json($payout, 201);
    }

    public function show(int $id): JsonResponse
    {
        $payout = Payout::with(['user'])->findOrFail($id);

        return response()->json($payout);
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $payout = Payout::where('status', 'pending')->findOrFail($id);

        DB::transaction(function () use ($payout, $request) {
            $payout->update([
                'status' => 'approved',
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
                'admin_notes' => $request->notes,
            ]);

            $payout->user->decrement('available_balance', $payout->amount);
        });

        Log::info("Payout approved: {$payout->id}");

        return response()->json([
            'message' => 'Payout approved',
            'payout' => $payout->fresh(),
        ]);
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        $payout = Payout::where('status', 'pending')->findOrFail($id);

        $payout->update([
            'status' => 'rejected',
            'rejected_by' => $request->user()->id,
            'rejected_at' => now(),
            'rejection_reason' => $request->reason,
        ]);

        Log::info("Payout rejected: {$payout->id}, reason: {$request->reason}");

        return response()->json([
            'message' => 'Payout rejected',
            'payout' => $payout->fresh(),
        ]);
    }

    public function process(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $payout = Payout::where('status', 'approved')->findOrFail($id);

        DB::transaction(function () use ($payout, $request) {
            $payout->update([
                'status' => 'completed',
                'processed_by' => $request->user()->id,
                'processed_at' => now(),
                'transaction_id' => $request->transaction_id ?? 'TXN-'.strtoupper(Str::random(12)),
                'admin_notes' => $request->notes,
            ]);

            Log::info("Payout processed: {$payout->id}, transaction: {$payout->transaction_id}");
        });

        return response()->json([
            'message' => 'Payout processed',
            'payout' => $payout->fresh(),
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $stats = Payout::selectRaw('
            COUNT(*) as total_payouts,
            SUM(CASE WHEN status = "pending" THEN amount ELSE 0 END) as pending_amount,
            SUM(CASE WHEN status = "approved" THEN amount ELSE 0 END) as approved_amount,
            SUM(CASE WHEN status = "completed" THEN amount ELSE 0 END) as completed_amount,
            SUM(CASE WHEN status = "rejected" THEN amount ELSE 0 END) as rejected_amount
        ')->first();

        return response()->json([
            'total_payouts' => $stats->total_payouts,
            'pending_amount' => $stats->pending_amount,
            'approved_amount' => $stats->approved_amount,
            'completed_amount' => $stats->completed_amount,
            'rejected_amount' => $stats->rejected_amount,
        ]);
    }

    public function instructorPayouts(Request $request, int $userId): JsonResponse
    {
        $payouts = Payout::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($payouts);
    }

    public function instructorBalance(int $userId): JsonResponse
    {
        $user = User::findOrFail($userId);

        $availableBalance = $this->getAvailableBalance($userId);
        $pendingBalance = $this->getPendingBalance($userId);
        $totalEarned = $this->getTotalEarned($userId);

        return response()->json([
            'available_balance' => $availableBalance,
            'pending_balance' => $pendingBalance,
            'total_earned' => $totalEarned,
            'currency' => 'USD',
        ]);
    }

    public function batchApprove(Request $request): JsonResponse
    {
        $request->validate([
            'payout_ids' => 'required|array',
            'payout_ids.*' => 'exists:payouts,id',
        ]);

        $count = 0;

        foreach ($request->payout_ids as $id) {
            $payout = Payout::where('status', 'pending')->find($id);

            if ($payout) {
                DB::transaction(function () use ($payout, $request) {
                    $payout->update([
                        'status' => 'approved',
                        'approved_by' => $request->user()->id,
                        'approved_at' => now(),
                    ]);

                    $payout->user->decrement('available_balance', $payout->amount);
                });
                $count++;
            }
        }

        return response()->json([
            'message' => "{$count} payouts approved",
            'approved_count' => $count,
        ]);
    }

    protected function getAvailableBalance(int $userId): float
    {
        return PaymentHistory::where('user_id', $userId)
            ->where('status', 'completed')
            ->where('payout_status', '!=', 'paid')
            ->sum('instructor_commission') -
            Payout::where('user_id', $userId)
                ->whereIn('status', ['pending', 'approved'])
                ->sum('amount');
    }

    protected function getPendingBalance(int $userId): float
    {
        return Payout::where('user_id', $userId)
            ->whereIn('status', ['pending', 'approved'])
            ->sum('amount');
    }

    protected function getTotalEarned(int $userId): float
    {
        return PaymentHistory::where('user_id', $userId)
            ->where('status', 'completed')
            ->sum('instructor_commission');
    }
}
