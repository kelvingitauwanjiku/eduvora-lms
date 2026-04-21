<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use App\Models\Course\Course;
use App\Models\Category;
use App\Models\User;
use App\Models\Blog\Blog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function global(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['message' => 'Query too short'], 400);
        }

        $results = [
            'courses' => Course::published()
                ->where('title', 'like', "%{$query}%")
                ->limit(5)
                ->get(['id', 'title', 'slug', 'thumbnail', 'price']),
            
            'instructors' => User::where('is_instructor', true)
                ->where(function ($q) use ($query) {
                    $q->where('first_name', 'like', "%{$query}%")
                        ->orWhere('last_name', 'like', "%{$query}%");
                })
                ->limit(5)
                ->get(['id', 'first_name', 'last_name', 'avatar']),
            
            'blogs' => Blog::published()
                ->where('title', 'like', "%{$query}%")
                ->limit(5)
                ->get(['id', 'title', 'slug', 'thumbnail']),
            
            'categories' => Category::active()
                ->where('name', 'like', "%{$query}%")
                ->limit(5)
                ->get(['id', 'name', 'slug', 'icon']),
        ];

        return response()->json($results);
    }

    public function advanced(Request $request): JsonResponse
    {
        $query = Course::query()->published();
        
        // Text search
        if ($request->has('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->q}%")
                    ->orWhere('short_description', 'like', "%{$request->q}%")
                    ->orWhere('description', 'like', "%{$request->q}%");
            });
        }

        // Filters
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        if ($request->has('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->has('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->has('rating')) {
            $query->where('average_rating', '>=', $request->rating);
        }

        if ($request->has('is_free')) {
            $query->where('is_paid', !$request->is_free);
        }

        // Sorting
        $sortBy = $request->get('sort', 'published_at');
        $sortDir = $request->get('dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $perPage = $request->get('per_page', 12);
        $results = $query->with(['user', 'category'])->paginate($perPage);

        return response()->json($results);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $suggestions = Course::published()
            ->select('id', 'title', 'slug')
            ->where('title', 'like', "{$query}%")
            ->limit(10)
            ->get();

        return response()->json($suggestions);
    }
}