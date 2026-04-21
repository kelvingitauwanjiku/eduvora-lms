<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course\Course;
use App\Models\TeamPackagePurchase;
use App\Models\TeamTrainingPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamTrainingController extends Controller
{
    public function index(Request $request)
    {
        $packages = TeamTrainingPackage::with(['instructor'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $packages,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:team_training_packages,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'max_members' => 'required|integer|min:2',
            'min_members' => 'nullable|integer|min:1',
            'thumbnail' => 'nullable|string',
            'preview_video' => 'nullable|string',
            'is_active' => 'boolean',
            'features' => 'nullable|array',
            'course_ids' => 'array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['user_id'] = auth()->id();

        $package = TeamTrainingPackage::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Team package created successfully',
            'data' => $package,
        ], 201);
    }

    public function show(int $id)
    {
        $package = TeamTrainingPackage::with(['instructor', 'courses'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $package,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $package = TeamTrainingPackage::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'slug' => 'string|unique:team_training_packages,slug,'.$id,
            'description' => 'nullable|string',
            'price' => 'numeric|min:0',
            'max_members' => 'integer|min:2',
            'min_members' => 'nullable|integer|min:1',
            'thumbnail' => 'nullable|string',
            'preview_video' => 'nullable|string',
            'is_active' => 'boolean',
            'features' => 'nullable|array',
            'course_ids' => 'array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $package->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Team package updated successfully',
            'data' => $package,
        ]);
    }

    public function destroy(int $id)
    {
        $package = TeamTrainingPackage::findOrFail($id);
        $package->delete();

        return response()->json([
            'success' => true,
            'message' => 'Team package deleted successfully',
        ]);
    }

    public function duplicate(int $id)
    {
        $package = TeamTrainingPackage::findOrFail($id);

        $newPackage = $package->replicate();
        $newPackage->title = $package->title.' (Copy)';
        $newPackage->slug = $package->slug.'-'.time();
        $newPackage->save();

        return response()->json([
            'success' => true,
            'message' => 'Team package duplicated successfully',
            'data' => $newPackage,
        ], 201);
    }

    public function toggleStatus(int $id)
    {
        $package = TeamTrainingPackage::findOrFail($id);
        $package->update(['is_active' => ! $package->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated',
            'is_active' => $package->is_active,
        ]);
    }

    public function purchaseHistory()
    {
        $purchases = TeamPackagePurchase::with(['user', 'package', 'members'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $purchases,
        ]);
    }

    public function invoice(int $id)
    {
        $purchase = TeamPackagePurchase::with(['user', 'package', 'members'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $purchase,
        ]);
    }

    public function getCourses(Request $request)
    {
        $privacy = $request->get('privacy', 'both');

        $courses = Course::query()
            ->when($privacy === 'public', fn ($q) => $q->where('is_public', true))
            ->when($privacy === 'private', fn ($q) => $q->where('is_public', false))
            ->where('is_active', true)
            ->select('id', 'title', 'price')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $courses,
        ]);
    }
}
