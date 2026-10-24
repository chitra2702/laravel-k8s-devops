
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Desk</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f5f9;
            color: #202938;
            margin: 0;
            padding: 35px 16px;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        .panel {
            background: white;
            padding: 24px;
            margin-bottom: 24px;
            border-radius: 12px;
            box-shadow: 0 3px 12px #0000000a;
        }

        input, textarea, select, button {
            box-sizing: border-box;
            font: inherit;
            padding: 11px;
            border: 1px solid #d5dbe5;
            border-radius: 6px;
        }

        input, textarea, .full {
            width: 100%;
            margin: 7px 0 14px;
        }

        button {
            background: #2458d3;
            color: white;
            border: none;
            cursor: pointer;
        }

        .ticket {
            border-top: 1px solid #e5e7eb;
            padding: 18px 0;
        }

        .meta {
            color: #626d7d;
            font-size: 14px;
        }

        .success {
            background: #e4f8eb;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 18px;
        }

        .error {
            color: #c62828;
        }

        .status-form {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 12px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Support Desk</h1>
    <p>Manage support tickets in one place.</p>

    @if (session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="panel error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="panel">
        <h2>Create a ticket</h2>

        <form action="{{ route('tickets.store') }}" method="POST">
            @csrf

            <label for="title">Ticket title</label>
            <input id="title" name="title" value="{{ old('title') }}"
                   required maxlength="150" placeholder="e.g. Unable to log in">

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"
                      required placeholder="Describe the problem">{{ old('description') }}</textarea>

            <label for="priority">Priority</label>
            <select id="priority" name="priority">
                <option value="low">Low</option>
                <option value="medium" selected>Medium</option>
                <option value="high">High</option>
            </select>

            <button type="submit">Create ticket</button>
        </form>
    </div>

    <div class="panel">
        <h2>Tickets ({{ $tickets->count() }})</h2>

        @forelse ($tickets as $ticket)
            <div class="ticket">
                <h3>#{{ $ticket->id }} — {{ $ticket->title }}</h3>

                <p>{{ $ticket->description }}</p>

                <p class="meta">
                    Priority: <strong>{{ ucfirst($ticket->priority) }}</strong>
                    · Status: <strong>{{ str_replace('_', ' ', ucfirst($ticket->status)) }}</strong>
                    · Created: {{ $ticket->created_at->format('d M Y, H:i') }}
                </p>

                <form class="status-form"
                      action="{{ route('tickets.status', $ticket) }}"
                      method="POST">
                    @csrf
                    @method('PATCH')

                    <label for="status-{{ $ticket->id }}">Update status</label>

                    <select id="status-{{ $ticket->id }}" name="status">
                        <option value="open" @selected($ticket->status === 'open')>Open</option>
                        <option value="in_progress" @selected($ticket->status === 'in_progress')>In progress</option>
                        <option value="resolved" @selected($ticket->status === 'resolved')>Resolved</option>
                    </select>

                    <button type="submit">Save status</button>
                </form>
            </div>
        @empty
            <p>No tickets yet. Create your first ticket above.</p>
        @endforelse
    </div>
</div>
</body>
</html>
