<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AffiliateCommission;
use App\Models\AffiliateReferral;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function index(Request $request)
    {
        $query = Affiliate::with('user');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $affiliates = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $affiliates,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'status' => 'nullable|in:active,inactive',
        ]);

        $affiliate = Affiliate::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Affiliate created successfully',
            'data' => $affiliate,
        ], 201);
    }

    public function show(int $id)
    {
        $affiliate = Affiliate::with('user', 'commissions', 'referrals')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $affiliate,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $affiliate = Affiliate::findOrFail($id);

        $validated = $request->validate([
            'commission_rate' => 'numeric|min:0|max:100',
            'status' => 'in:active,inactive',
        ]);

        $affiliate->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Affiliate updated successfully',
            'data' => $affiliate,
        ]);
    }

    public function destroy(int $id)
    {
        $affiliate = Affiliate::findOrFail($id);
        $affiliate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Affiliate deleted successfully',
        ]);
    }

    public function referrals()
    {
        $referrals = AffiliateReferral::with(['affiliate.user', 'referredUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $referrals,
        ]);
    }

    public function commissions(Request $request)
    {
        $query = AffiliateCommission::with(['affiliate.user', 'referral']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $commissions = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $commissions,
        ]);
    }

    public function approveCommission(int $id)
    {
        $commission = AffiliateCommission::findOrFail($id);
        $commission->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => 'Commission approved',
        ]);
    }

    public function rejectCommission(int $id)
    {
        $commission = AffiliateCommission::findOrFail($id);
        $commission->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Commission rejected',
        ]);
    }

    public function statistics()
    {
        $totalAffiliates = Affiliate::count();
        $activeAffiliates = Affiliate::where('status', 'active')->count();
        $totalReferrals = AffiliateReferral::count();
        $totalCommissions = AffiliateCommission::sum('amount');
        $pendingCommissions = AffiliateCommission::where('status', 'pending')->sum('amount');

        return response()->json([
            'success' => true,
            'data' => [
                'total_affiliates' => $totalAffiliates,
                'active_affiliates' => $activeAffiliates,
                'total_referrals' => $totalReferrals,
                'total_commissions' => $totalCommissions,
                'pending_commissions' => $pendingCommissions,
            ],
        ]);
    }
}
