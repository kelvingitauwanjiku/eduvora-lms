<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionFeature;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function plans()
    {
        $plans = SubscriptionPlan::with('features')->orderBy('price')->get();

        return response()->json([
            'success' => true,
            'data' => $plans,
        ]);
    }

    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:subscription_plans,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly,lifetime',
            'max_courses' => 'nullable|integer|min:-1',
            'max_students' => 'nullable|integer|min:-1',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);

        $plan = SubscriptionPlan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Plan created successfully',
            'data' => $plan,
        ], 201);
    }

    public function updatePlan(Request $request, int $id)
    {
        $plan = SubscriptionPlan::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'slug' => 'string|unique:subscription_plans,slug,'.$id,
            'description' => 'nullable|string',
            'price' => 'numeric|min:0',
            'billing_cycle' => 'in:monthly,yearly,lifetime',
            'max_courses' => 'nullable|integer|min:-1',
            'max_students' => 'nullable|integer|min:-1',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $plan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Plan updated successfully',
            'data' => $plan,
        ]);
    }

    public function deletePlan(int $id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Plan deleted successfully',
        ]);
    }

    public function planFeatures(int $planId)
    {
        $features = SubscriptionFeature::where('subscription_plan_id', $planId)->get();

        return response()->json([
            'success' => true,
            'data' => $features,
        ]);
    }

    public function storeFeature(Request $request)
    {
        $validated = $request->validate([
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'name' => 'required|string|max:255',
            'value' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $feature = SubscriptionFeature::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Feature created successfully',
            'data' => $feature,
        ], 201);
    }

    public function deleteFeature(int $id)
    {
        $feature = SubscriptionFeature::findOrFail($id);
        $feature->delete();

        return response()->json([
            'success' => true,
            'message' => 'Feature deleted successfully',
        ]);
    }

    public function subscriptions(Request $request)
    {
        $query = Subscription::with(['user', 'plan']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $subscriptions,
        ]);
    }

    public function cancelSubscription(int $id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled',
        ]);
    }
}
