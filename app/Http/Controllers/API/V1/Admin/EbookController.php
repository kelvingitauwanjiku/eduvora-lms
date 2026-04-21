<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ebook;
use App\Models\EbookCategory;
use App\Models\EbookPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EbookController extends Controller
{
    public function index(Request $request)
    {
        $query = Ebook::with(['category', 'instructor']);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $ebooks = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $ebooks,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:ebooks,slug',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:ebook_categories,id',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'thumbnail' => 'nullable|string',
            'file_url' => 'nullable|string',
            'preview_content' => 'nullable|string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|string',
            'pages' => 'nullable|integer',
            'published_date' => 'nullable|date',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);

        $ebook = Ebook::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Ebook created successfully',
            'data' => $ebook,
        ], 201);
    }

    public function show(int $id)
    {
        $ebook = Ebook::with(['category', 'instructor', 'reviews'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $ebook,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $ebook = Ebook::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'slug' => 'string|unique:ebooks,slug,'.$id,
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:ebook_categories,id',
            'price' => 'numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'thumbnail' => 'nullable|string',
            'file_url' => 'nullable|string',
            'preview_content' => 'nullable|string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|string',
            'pages' => 'nullable|integer',
            'published_date' => 'nullable|date',
        ]);

        $ebook->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Ebook updated successfully',
            'data' => $ebook,
        ]);
    }

    public function destroy(int $id)
    {
        $ebook = Ebook::findOrFail($id);
        $ebook->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ebook deleted successfully',
        ]);
    }

    public function status(int $id)
    {
        $ebook = Ebook::findOrFail($id);
        $ebook->update(['is_active' => ! $ebook->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $ebook->is_active,
        ]);
    }

    public function purchaseHistory()
    {
        $purchases = EbookPurchase::with(['user', 'ebook'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $purchases,
        ]);
    }

    public function adminRevenue(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());

        $revenue = EbookPurchase::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');

        return response()->json([
            'success' => true,
            'total_revenue' => $revenue,
        ]);
    }

    public function instructorRevenue(Request $request, int $instructorId)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());

        $revenue = EbookPurchase::where('instructor_id', $instructorId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');

        return response()->json([
            'success' => true,
            'total_revenue' => $revenue,
        ]);
    }

    public function preview(string $slug, string $type)
    {
        $ebook = Ebook::where('slug', $slug)->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $ebook,
            'preview_type' => $type,
        ]);
    }

    public function categories()
    {
        $categories = EbookCategory::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:ebook_categories,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);

        $category = EbookCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $category,
        ], 201);
    }

    public function updateCategory(Request $request, int $id)
    {
        $category = EbookCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'slug' => 'string|unique:ebook_categories,slug,'.$id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category,
        ]);
    }

    public function deleteCategory(int $id)
    {
        $category = EbookCategory::findOrFail($id);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ]);
    }
}
