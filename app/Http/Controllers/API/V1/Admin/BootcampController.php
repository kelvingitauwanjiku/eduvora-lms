<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bootcamp\Bootcamp;
use App\Models\Bootcamp\BootcampLiveClass;
use App\Models\Bootcamp\BootcampModule;
use App\Models\Bootcamp\BootcampPurchase;
use App\Models\Bootcamp\BootcampResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BootcampController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'all');

        $query = Bootcamp::with(['category', 'instructor']);

        if ($type === 'active') {
            $query->where('is_active', true);
        } elseif ($type === 'pending') {
            $query->where('is_active', false);
        }

        $bootcamps = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $bootcamps,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:bootcamps,slug',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'thumbnail' => 'nullable|string',
            'preview_video' => 'nullable|string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'total_seats' => 'nullable|integer|min:1',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);

        $bootcamp = Bootcamp::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bootcamp created successfully',
            'data' => $bootcamp,
        ], 201);
    }

    public function show(int $id)
    {
        $bootcamp = Bootcamp::with(['category', 'instructor', 'modules', 'resources', 'liveClasses'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $bootcamp,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $bootcamp = Bootcamp::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'slug' => 'string|unique:bootcamps,slug,'.$id,
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'thumbnail' => 'nullable|string',
            'preview_video' => 'nullable|string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'total_seats' => 'nullable|integer|min:1',
        ]);

        $bootcamp->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bootcamp updated successfully',
            'data' => $bootcamp,
        ]);
    }

    public function destroy(int $id)
    {
        $bootcamp = Bootcamp::findOrFail($id);
        $bootcamp->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bootcamp deleted successfully',
        ]);
    }

    public function status(int $id)
    {
        $bootcamp = Bootcamp::findOrFail($id);
        $bootcamp->update(['is_active' => ! $bootcamp->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Bootcamp status updated',
            'is_active' => $bootcamp->is_active,
        ]);
    }

    public function duplicate(int $id)
    {
        $bootcamp = Bootcamp::findOrFail($id);

        $newBootcamp = $bootcamp->replicate();
        $newBootcamp->title = $bootcamp->title.' (Copy)';
        $newBootcamp->slug = $bootcamp->slug.'-'.time();
        $newBootcamp->save();

        return response()->json([
            'success' => true,
            'message' => 'Bootcamp duplicated successfully',
            'data' => $newBootcamp,
        ], 201);
    }

    public function purchaseHistory()
    {
        $purchases = BootcampPurchase::with(['user', 'bootcamp'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $purchases,
        ]);
    }

    public function invoice(int $id)
    {
        $purchase = BootcampPurchase::with(['user', 'bootcamp'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $purchase,
        ]);
    }

    public function modules(Request $request)
    {
        $validated = $request->validate([
            'bootcamp_id' => 'required|exists:bootcamps,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $module = BootcampModule::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Module created successfully',
            'data' => $module,
        ], 201);
    }

    public function updateModule(Request $request, int $id)
    {
        $module = BootcampModule::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $module->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Module updated successfully',
            'data' => $module,
        ]);
    }

    public function deleteModule(int $id)
    {
        $module = BootcampModule::findOrFail($id);
        $module->delete();

        return response()->json([
            'success' => true,
            'message' => 'Module deleted successfully',
        ]);
    }

    public function sortModules(Request $request)
    {
        $request->validate([
            'modules' => 'required|array',
            'modules.*.id' => 'required|exists:bootcamp_modules,id',
            'modules.*.order' => 'required|integer',
        ]);

        foreach ($request->input('modules') as $moduleData) {
            BootcampModule::where('id', $moduleData['id'])->update(['order' => $moduleData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Modules sorted successfully',
        ]);
    }

    public function resources(Request $request)
    {
        $validated = $request->validate([
            'bootcamp_id' => 'required|exists:bootcamps,id',
            'module_id' => 'nullable|exists:bootcamp_modules,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:pdf,video,document,link',
            'file_url' => 'nullable|string',
            'file_path' => 'nullable|string',
            'duration' => 'nullable|integer',
            'is_preview' => 'boolean',
        ]);

        $resource = BootcampResource::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Resource created successfully',
            'data' => $resource,
        ], 201);
    }

    public function deleteResource(int $id)
    {
        $resource = BootcampResource::findOrFail($id);
        $resource->delete();

        return response()->json([
            'success' => true,
            'message' => 'Resource deleted successfully',
        ]);
    }

    public function liveClasses(Request $request)
    {
        $validated = $request->validate([
            'bootcamp_id' => 'required|exists:bootcamps,id',
            'module_id' => 'nullable|exists:bootcamp_modules,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'required|date',
            'duration' => 'required|integer|min:15',
            'meeting_link' => 'nullable|string',
            'instructor_id' => 'nullable|exists:users,id',
        ]);

        $liveClass = BootcampLiveClass::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Live class scheduled successfully',
            'data' => $liveClass,
        ], 201);
    }

    public function updateLiveClass(Request $request, int $id)
    {
        $liveClass = BootcampLiveClass::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'date',
            'duration' => 'integer|min:15',
            'meeting_link' => 'nullable|string',
            'instructor_id' => 'nullable|exists:users,id',
            'status' => 'in:scheduled,live,ended,cancelled',
        ]);

        $liveClass->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Live class updated successfully',
            'data' => $liveClass,
        ]);
    }

    public function deleteLiveClass(int $id)
    {
        $liveClass = BootcampLiveClass::findOrFail($id);
        $liveClass->delete();

        return response()->json([
            'success' => true,
            'message' => 'Live class deleted successfully',
        ]);
    }

    public function joinLiveClass(int $id)
    {
        $liveClass = BootcampLiveClass::findOrFail($id);

        return response()->json([
            'success' => true,
            'meeting_link' => $liveClass->meeting_link,
            'status' => $liveClass->status,
        ]);
    }

    public function endLiveClass(int $id)
    {
        $liveClass = BootcampLiveClass::findOrFail($id);
        $liveClass->update(['status' => 'ended']);

        return response()->json([
            'success' => true,
            'message' => 'Live class ended',
        ]);
    }
}
