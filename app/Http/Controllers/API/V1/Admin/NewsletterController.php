<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $newsletters = Newsletter::orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $newsletters,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'nullable|in:draft,scheduled,sent',
            'scheduled_at' => 'nullable|date',
        ]);

        $newsletter = Newsletter::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Newsletter created successfully',
            'data' => $newsletter,
        ], 201);
    }

    public function show(int $id)
    {
        $newsletter = Newsletter::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $newsletter,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $newsletter = Newsletter::findOrFail($id);

        $validated = $request->validate([
            'subject' => 'string|max:255',
            'content' => 'string',
            'status' => 'in:draft,scheduled,sent',
            'scheduled_at' => 'nullable|date',
        ]);

        $newsletter->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Newsletter updated successfully',
            'data' => $newsletter,
        ]);
    }

    public function destroy(int $id)
    {
        $newsletter = Newsletter::findOrFail($id);
        $newsletter->delete();

        return response()->json([
            'success' => true,
            'message' => 'Newsletter deleted successfully',
        ]);
    }

    public function send(int $id)
    {
        $newsletter = Newsletter::findOrFail($id);
        $newsletter->update(['status' => 'sent']);

        return response()->json([
            'success' => true,
            'message' => 'Newsletter sending started',
        ]);
    }

    public function subscribers()
    {
        $subscribers = NewsletterSubscriber::orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $subscribers,
        ]);
    }

    public function deleteSubscriber(int $id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);
        $subscriber->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subscriber deleted successfully',
        ]);
    }

    public function statistics()
    {
        $totalSubscribers = NewsletterSubscriber::count();
        $activeSubscribers = NewsletterSubscriber::where('is_active', true)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_subscribers' => $totalSubscribers,
                'active_subscribers' => $activeSubscribers,
            ],
        ]);
    }
}
