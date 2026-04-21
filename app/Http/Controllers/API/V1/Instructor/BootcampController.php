<?php

namespace App\Http\Controllers\Api\V1\Instructor;

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
        $bootcamps = Bootcamp::with('category')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

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
        $validated['user_id'] = auth()->id();

        $bootcamp = Bootcamp::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bootcamp created successfully',
            'data' => $bootcamp,
        ], 201);
    }

    public function show(int $id)
    {
        $bootcamp = Bootcamp::with(['category', 'modules', 'resources', 'liveClasses'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $bootcamp,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $bootcamp = Bootcamp::where('user_id', auth()->id())->findOrFail($id);

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
        $bootcamp = Bootcamp::where('user_id', auth()->id())->findOrFail($id);
        $bootcamp->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bootcamp deleted successfully',
        ]);
    }

    public function status(int $id)
    {
        $bootcamp = Bootcamp::where('user_id', auth()->id())->findOrFail($id);
        $bootcamp->update(['is_active' => ! $bootcamp->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $bootcamp->is_active,
        ]);
    }

    public function duplicate(int $id)
    {
        $bootcamp = Bootcamp::findOrFail($id);

        $newBootcamp = $bootcamp->replicate();
        $newBootcamp->title = $bootcamp->title.' (Copy)';
        $newBootcamp->slug = $bootcamp->slug.'-'.time();
        $newBootcamp->is_active = false;
        $newBootcamp->save();

        return response()->json([
            'success' => true,
            'message' => 'Bootcamp duplicated successfully',
            'data' => $newBootcamp,
        ], 201);
    }

    public function purchaseHistory(int $id)
    {
        $bootcamp = Bootcamp::findOrFail($id);

        $purchases = BootcampPurchase::with('user')
            ->where('bootcamp_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $purchases,
        ]);
    }

    public function invoice(int $id)
    {
        $purchase = BootcampPurchase::with(['user', 'bootcamp'])
            ->findOrFail($id);

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
        ]);

        $validated['instructor_id'] = auth()->id();

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
}
