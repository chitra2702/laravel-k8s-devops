<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::latest()->get();
       
        return view('tickets.index', compact('tickets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:5000'],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
        ]);

        Ticket::create($validated);

        return redirect()
            ->route('tickets.index')
            ->with('success', 'Ticket created successfully!');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(['open', 'in_progress', 'resolved']),
            ],
        ]);

        $ticket->update($validated);

        return redirect()
            ->route('tickets.index')
            ->with('success', 'Ticket status updated!');
    }
}
