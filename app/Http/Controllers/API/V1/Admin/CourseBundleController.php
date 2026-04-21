<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\BundleCourse;
use App\Models\BundlePurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseBundleController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'all');

        $query = Bundle::with(['courses', 'instructor']);

        if ($type === 'active') {
            $query->where('is_active', true);
        } elseif ($type === 'inactive') {
            $query->where('is_active', false);
        }

        $bundles = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $bundles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:bundles,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'thumbnail' => 'nullable|string',
            'preview_video' => 'nullable|string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'course_ids' => 'array',
            'course_ids.*' => 'exists:courses,id',
            'subscription_enabled' => 'boolean',
            'subscription_price' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['user_id'] = auth()->id();

        $bundle = Bundle::create($validated);

        if (! empty($validated['course_ids'])) {
            foreach ($validated['course_ids'] as $courseId) {
                BundleCourse::create([
                    'bundle_id' => $bundle->id,
                    'course_id' => $courseId,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Bundle created successfully',
            'data' => $bundle->load('courses'),
        ], 201);
    }

    public function show(int $id)
    {
        $bundle = Bundle::with(['courses', 'instructor', 'purchases'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $bundle,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $bundle = Bundle::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'slug' => 'string|unique:bundles,slug,'.$id,
            'description' => 'nullable|string',
            'price' => 'numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'thumbnail' => 'nullable|string',
            'preview_video' => 'nullable|string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'course_ids' => 'array',
            'course_ids.*' => 'exists:courses,id',
            'subscription_enabled' => 'boolean',
            'subscription_price' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $bundle->update($validated);

        if (isset($validated['course_ids'])) {
            BundleCourse::where('bundle_id', $bundle->id)->delete();
            foreach ($validated['course_ids'] as $courseId) {
                BundleCourse::create([
                    'bundle_id' => $bundle->id,
                    'course_id' => $courseId,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Bundle updated successfully',
            'data' => $bundle->load('courses'),
        ]);
    }

    public function destroy(int $id)
    {
        $bundle = Bundle::findOrFail($id);
        BundleCourse::where('bundle_id', $bundle->id)->delete();
        $bundle->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bundle deleted successfully',
        ]);
    }

    public function status(Request $request, int $id)
    {
        $bundle = Bundle::findOrFail($id);
        $type = $request->get('type', 'active');

        $bundle->update(['is_active' => $type === 'active']);

        return response()->json([
            'success' => true,
            'message' => 'Bundle status updated',
            'is_active' => $bundle->is_active,
        ]);
    }

    public function duplicate(int $id)
    {
        $bundle = Bundle::with('courses')->findOrFail($id);

        $newBundle = $bundle->replicate();
        $newBundle->title = $bundle->title.' (Copy)';
        $newBundle->slug = $bundle->slug.'-'.time();
        $newBundle->save();

        foreach ($bundle->courses as $course) {
            BundleCourse::create([
                'bundle_id' => $newBundle->id,
                'course_id' => $course->id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Bundle duplicated successfully',
            'data' => $newBundle->load('courses'),
        ], 201);
    }

    public function purchaseHistory(Request $request)
    {
        $purchases = BundlePurchase::with(['user', 'bundle'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $purchases,
        ]);
    }

    public function invoice(int $id)
    {
        $purchase = BundlePurchase::with(['user', 'bundle.courses'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $purchase,
        ]);
    }

    public function adminRevenue(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());

        $revenue = BundlePurchase::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');

        $purchases = BundlePurchase::with(['user', 'bundle'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'total_revenue' => $revenue,
            'purchases' => $purchases,
        ]);
    }

    public function instructorRevenue(Request $request, int $instructorId)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());

        $bundles = Bundle::where('user_id', $instructorId)->get();
        $bundleIds = $bundles->pluck('id');

        $revenue = BundlePurchase::whereIn('bundle_id', $bundleIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');

        return response()->json([
            'success' => true,
            'total_revenue' => $revenue,
            'bundles' => $bundles,
        ]);
    }
}
