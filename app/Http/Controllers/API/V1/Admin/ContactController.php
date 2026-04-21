<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $messages = Message::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $message = Message::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $message,
        ], 201);
    }

    public function show(int $id)
    {
        $message = Message::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $message,
        ]);
    }

    public function reply(Request $request, int $id)
    {
        $message = Message::findOrFail($id);

        $validated = $request->validate([
            'reply' => 'required|string',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reply sent successfully',
        ]);
    }

    public function destroy(int $id)
    {
        $message = Message::findOrFail($id);
        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully',
        ]);
    }
}
