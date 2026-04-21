<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Support\Ticket;
use App\Models\Support\TicketCategory;
use App\Models\Support\TicketMessage;
use App\Models\Support\TicketPriority;
use App\Models\Support\TicketStatus;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index(Request $request)
    {
        $tickets = Ticket::with(['category', 'priority', 'status', 'user'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $tickets,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:ticket_categories,id',
            'priority_id' => 'required|exists:ticket_priorities,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status_id'] = TicketStatus::where('name', 'open')->first()->id ?? 1;
        $validated['ticket_code'] = 'TKT-'.strtoupper(uniqid());

        $ticket = Ticket::create($validated);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_admin' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket created successfully',
            'data' => $ticket->load('category', 'priority', 'status'),
        ], 201);
    }

    public function show(int $id)
    {
        $ticket = Ticket::with(['category', 'priority', 'status', 'user', 'messages.user'])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $ticket,
        ]);
    }

    public function reply(Request $request, int $id)
    {
        $ticket = Ticket::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_admin' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reply added successfully',
            'data' => $message,
        ], 201);
    }

    public function categories()
    {
        $categories = TicketCategory::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function priorities()
    {
        $priorities = TicketPriority::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => $priorities,
        ]);
    }
}
