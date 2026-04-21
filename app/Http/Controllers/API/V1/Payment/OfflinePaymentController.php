<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use App\Models\Course\Course;
use App\Models\PaymentHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OfflinePaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $payments = PaymentHistory::where('payment_method', 'offline')
            ->with(['course', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($payments);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'bank_name' => 'required|string',
            'transaction_number' => 'required|string|unique:payment_histories,transaction_id',
            'amount' => 'required|numeric|min:0',
            'transfer_date' => 'required|date',
            'transfer_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'notes' => 'nullable|string',
        ]);

        $course = Course::findOrFail($request->course_id);
        $price = $course->discount_price ?? $course->price;

        if ($request->amount < $price) {
            return response()->json([
                'error' => 'Amount is less than course price',
                'course_price' => $price,
            ], 400);
        }

        $proofPath = null;
        if ($request->hasFile('transfer_proof')) {
            $proofPath = $request->file('transfer_proof')->store('offline-payments', 'public');
        }

        $payment = PaymentHistory::create([
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
            'order_number' => 'ORD-OFF-'.strtoupper(Str::random(8)),
            'transaction_id' => $request->transaction_number,
            'currency' => 'USD',
            'subtotal' => $request->amount,
            'total_amount' => $request->amount,
            'amount' => $request->amount,
            'payment_method' => 'offline',
            'status' => 'pending',
            'offline_details' => [
                'bank_name' => $request->bank_name,
                'transfer_date' => $request->transfer_date,
                'transfer_proof' => $proofPath,
                'notes' => $request->notes,
            ],
        ]);

        return response()->json([
            'message' => 'Offline payment request submitted',
            'payment' => $payment,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $payment = PaymentHistory::where('payment_method', 'offline')
            ->with(['course', 'user'])
            ->findOrFail($id);

        return response()->json($payment);
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        $payment = PaymentHistory::where('payment_method', 'offline')
            ->findOrFail($id);

        if ($payment->status !== 'pending') {
            return response()->json([
                'error' => 'Payment is not pending',
                'current_status' => $payment->status,
            ], 400);
        }

        DB::transaction(function () use ($payment, $request) {
            $payment->update([
                'status' => 'completed',
                'completed_at' => now(),
                'admin_notes' => $request->admin_notes,
            ]);

            $payment->course->increment('students_count');
            $payment->course->user->increment('total_earnings', $payment->instructor_commission);
        });

        return response()->json([
            'message' => 'Payment approved',
            'payment' => $payment->fresh(),
        ]);
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        $payment = PaymentHistory::where('payment_method', 'offline')
            ->findOrFail($id);

        if ($payment->status !== 'pending') {
            return response()->json([
                'error' => 'Payment is not pending',
                'current_status' => $payment->status,
            ], 400);
        }

        $payment->update([
            'status' => 'rejected',
            'failure_reason' => $request->reason,
            'completed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Payment rejected',
            'payment' => $payment->fresh(),
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'bank_name' => 'sometimes|string',
            'transaction_number' => 'sometimes|string',
            'amount' => 'sometimes|numeric|min:0',
            'transfer_date' => 'sometimes|date',
            'notes' => 'sometimes|string',
        ]);

        $payment = PaymentHistory::where('payment_method', 'offline')
            ->where('status', 'pending')
            ->findOrFail($id);

        $offlineDetails = $payment->offline_details ?? [];

        if ($request->has('bank_name')) {
            $offlineDetails['bank_name'] = $request->bank_name;
        }
        if ($request->has('transaction_number')) {
            $offlineDetails['transaction_number'] = $request->transaction_number;
        }
        if ($request->has('transfer_date')) {
            $offlineDetails['transfer_date'] = $request->transfer_date;
        }
        if ($request->has('notes')) {
            $offlineDetails['notes'] = $request->notes;
        }

        $payment->update([
            'offline_details' => $offlineDetails,
            'total_amount' => $request->amount ?? $payment->total_amount,
            'amount' => $request->amount ?? $payment->amount,
        ]);

        return response()->json([
            'message' => 'Payment updated',
            'payment' => $payment->fresh(),
        ]);
    }

    public function uploadProof(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'transfer_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $payment = PaymentHistory::where('payment_method', 'offline')
            ->where('status', 'pending')
            ->findOrFail($id);

        if ($payment->offline_details['transfer_proof'] ?? null) {
            Storage::disk('public')->delete($payment->offline_details['transfer_proof']);
        }

        $proofPath = $request->file('transfer_proof')->store('offline-payments', 'public');

        $offlineDetails = $payment->offline_details ?? [];
        $offlineDetails['transfer_proof'] = $proofPath;

        $payment->update(['offline_details' => $offlineDetails]);

        return response()->json([
            'message' => 'Proof uploaded',
            'proof_url' => Storage::disk('public')->url($proofPath),
        ]);
    }

    public function bankDetails(Request $request): JsonResponse
    {
        $useTestMode = $request->boolean('test_mode', false);

        if ($useTestMode) {
            return response()->json([
                'bank_name' => 'Test Bank',
                'account_number' => '1234567890',
                'account_name' => 'Eduvora Test Account',
                'routing_number' => 'TEST123',
                'swift_code' => 'TESTUS33',
                'iban' => 'TEST123456789',
                'branch' => 'Test Branch',
                'test_mode' => true,
            ]);
        }

        return response()->json([
            'bank_name' => config('settings.offline_payment.bank_name'),
            'account_number' => config('settings.offline_payment.account_number'),
            'account_name' => config('settings.offline_payment.account_name'),
            'routing_number' => config('settings.offline_payment.routing_number'),
            'swift_code' => config('settings.offline_payment.swift_code'),
            'iban' => config('settings.offline_payment.iban'),
            'branch' => config('settings.offline_payment.branch'),
            'instructions' => config('settings.offline_payment.instructions'),
        ]);
    }

    public function stats(): JsonResponse
    {
        $stats = PaymentHistory::where('payment_method', 'offline')
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN status = "completed" THEN amount ELSE 0 END) as total_amount
            ')->first();

        return response()->json([
            'total' => $stats->total,
            'pending' => $stats->pending,
            'approved' => $stats->approved,
            'rejected' => $stats->rejected,
            'total_amount' => $stats->total_amount,
        ]);
    }
}
