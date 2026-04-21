<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\BundlePurchase;

class MyBundlesController extends Controller
{
    public function index()
    {
        $purchases = BundlePurchase::with(['bundle', 'bundle.courses'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $purchases,
        ]);
    }

    public function show(string $slug)
    {
        $bundle = Bundle::with('courses')->where('slug', $slug)->firstOrFail();

        $purchase = BundlePurchase::where('bundle_id', $bundle->id)
            ->where('user_id', auth()->id())
            ->first();

        if (! $purchase) {
            return response()->json([
                'success' => false,
                'error' => 'Not purchased',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $bundle,
            'purchase' => $purchase,
        ]);
    }

    public function invoice(int $id)
    {
        $purchase = BundlePurchase::with(['bundle', 'bundle.courses'])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $purchase,
        ]);
    }

    public function purchaseHistory()
    {
        $purchases = BundlePurchase::with('bundle')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $purchases,
        ]);
    }
}
