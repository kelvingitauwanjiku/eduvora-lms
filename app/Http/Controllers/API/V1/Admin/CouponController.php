<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $coupons = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $coupons,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'uses' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
            'course_ids' => 'nullable|array',
            'category_ids' => 'nullable|array',
        ]);

        $coupon = Coupon::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Coupon created successfully',
            'data' => $coupon,
        ], 201);
    }

    public function show(int $id)
    {
        $coupon = Coupon::with(['courses', 'categories'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $coupon,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $coupon = Coupon::findOrFail($id);

        $validated = $request->validate([
            'code' => 'string|max:50|unique:coupons,code,'.$id,
            'type' => 'in:fixed,percentage',
            'value' => 'numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'uses' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'is_active' => 'boolean',
            'course_ids' => 'nullable|array',
            'category_ids' => 'nullable|array',
        ]);

        $coupon->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Coupon updated successfully',
            'data' => $coupon,
        ]);
    }

    public function destroy(int $id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Coupon deleted successfully',
        ]);
    }

    public function status(int $id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->update(['is_active' => ! $coupon->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $coupon->is_active,
        ]);
    }

    public function validateCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'total' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $validated['code'])->first();

        if (! $coupon) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid coupon code',
            ], 404);
        }

        if (! $coupon->is_active) {
            return response()->json([
                'success' => false,
                'error' => 'Coupon is not active',
            ], 400);
        }

        if ($coupon->start_date && now()->lt($coupon->start_date)) {
            return response()->json([
                'success' => false,
                'error' => 'Coupon is not yet valid',
            ], 400);
        }

        if ($coupon->end_date && now()->gt($coupon->end_date)) {
            return response()->json([
                'success' => false,
                'error' => 'Coupon has expired',
            ], 400);
        }

        if ($coupon->max_uses && $coupon->uses >= $coupon->max_uses) {
            return response()->json([
                'success' => false,
                'error' => 'Coupon usage limit reached',
            ], 400);
        }

        if ($coupon->min_purchase && $validated['total'] < $coupon->min_purchase) {
            return response()->json([
                'success' => false,
                'error' => 'Minimum purchase requirement not met',
                'min_purchase' => $coupon->min_purchase,
            ], 400);
        }

        $discount = $coupon->type === 'percentage'
            ? ($validated['total'] * $coupon->value / 100)
            : $coupon->value;

        return response()->json([
            'success' => true,
            'discount' => $discount,
            'coupon' => $coupon,
        ]);
    }
}
