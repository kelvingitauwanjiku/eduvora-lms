<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $query = Faq::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $faqs = $query->orderBy('order')->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $faqs,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $faq = Faq::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'FAQ created successfully',
            'data' => $faq,
        ], 201);
    }

    public function show(int $id)
    {
        $faq = Faq::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $faq,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $faq = Faq::findOrFail($id);

        $validated = $request->validate([
            'question' => 'string',
            'answer' => 'string',
            'category' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $faq->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'FAQ updated successfully',
            'data' => $faq,
        ]);
    }

    public function destroy(int $id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();

        return response()->json([
            'success' => true,
            'message' => 'FAQ deleted successfully',
        ]);
    }

    public function sort(Request $request)
    {
        $request->validate([
            'faqs' => 'required|array',
            'faqs.*.id' => 'required|exists:faqs,id',
            'faqs.*.order' => 'required|integer',
        ]);

        foreach ($request->input('faqs') as $faqData) {
            Faq::where('id', $faqData['id'])->update(['order' => $faqData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'FAQs sorted successfully',
        ]);
    }
}
