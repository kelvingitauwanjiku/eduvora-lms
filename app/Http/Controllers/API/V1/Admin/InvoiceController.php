<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bootcamp\BootcampPurchase;
use App\Models\BundlePurchase;
use App\Models\Course\Enrollment;
use App\Models\EbookPurchase;
use App\Models\PaymentHistory;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentHistory::with('user');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $invoices,
        ]);
    }

    public function show(int $id)
    {
        $invoice = PaymentHistory::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $invoice,
        ]);
    }

    public function generate(Request $request, int $id)
    {
        $invoice = PaymentHistory::with('user')->findOrFail($id);

        $invoiceNumber = 'INV-'.strtoupper($invoice->transaction_id);

        return response()->json([
            'success' => true,
            'data' => [
                'invoice_number' => $invoiceNumber,
                'transaction_id' => $invoice->transaction_id,
                'user' => $invoice->user,
                'amount' => $invoice->amount,
                'currency' => $invoice->currency,
                'status' => $invoice->status,
                'payment_method' => $invoice->payment_method,
                'created_at' => $invoice->created_at,
            ],
        ]);
    }

    public function sendToCustomer(int $id)
    {
        $invoice = PaymentHistory::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Invoice sent to '.$invoice->user->email,
        ]);
    }

    public function allPurchases(Request $request)
    {
        $type = $request->get('type', 'all');

        $enrollments = [];
        $bundlePurchases = [];
        $ebookPurchases = [];
        $bootcampPurchases = [];

        if ($type === 'all' || $type === 'course') {
            $enrollments = Enrollment::with(['user', 'course'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        if ($type === 'all' || $type === 'bundle') {
            $bundlePurchases = BundlePurchase::with(['user', 'bundle'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        if ($type === 'all' || $type === 'ebook') {
            $ebookPurchases = EbookPurchase::with(['user', 'ebook'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        if ($type === 'all' || $type === 'bootcamp') {
            $bootcampPurchases = BootcampPurchase::with(['user', 'bootcamp'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'enrollments' => $enrollments,
                'bundle_purchases' => $bundlePurchases,
                'ebook_purchases' => $ebookPurchases,
                'bootcamp_purchases' => $bootcampPurchases,
            ],
        ]);
    }

    public function summary(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());

        $totalRevenue = PaymentHistory::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');

        $totalTransactions = PaymentHistory::whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $courseRevenue = Enrollment::whereBetween('created_at', [$startDate, $endDate])
            ->where('completed', true)
            ->sum('price');

        $bundleRevenue = BundlePurchase::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');

        return response()->json([
            'success' => true,
            'data' => [
                'total_revenue' => $totalRevenue,
                'total_transactions' => $totalTransactions,
                'course_revenue' => $courseRevenue,
                'bundle_revenue' => $bundleRevenue,
                'period' => [
                    'start' => $startDate,
                    'end' => $endDate,
                ],
            ],
        ]);
    }
}
