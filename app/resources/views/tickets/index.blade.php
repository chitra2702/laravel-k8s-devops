<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Desk</title>

    <style>
        :root {
            --bg: #f1f4f9;
            --card: #ffffff;
            --text: #1e2735;
            --muted: #6b7686;
            --border: #e3e8ef;
            --primary: #4f46e5;
            --primary-dark: #4338ca;
        }

        * { box-sizing: border-box; }

        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            margin: 0;
        }

        .header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff;
            padding: 40px 16px 70px;
        }

        .header-inner, .container { max-width: 880px; margin: 0 auto; }
        .header h1 { margin: 0 0 6px; font-size: 30px; }
        .header p  { margin: 0; opacity: .85; }

        .container { padding: 0 16px 40px; margin-top: -40px; }

        .panel {
            background: var(--card);
            padding: 26px;
            margin-bottom: 22px;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(30, 39, 53, .08);
        }

        .panel h2 { margin-top: 0; font-size: 20px; }

        label { font-size: 14px; font-weight: 600; color: var(--muted); }

        input, textarea, select, button {
            font: inherit;
            padding: 11px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
        }

        input, textarea { width: 100%; margin: 6px 0 16px; }
        input:focus, textarea:focus, select:focus {
            outline: 2px solid #c7d2fe;
            border-color: var(--primary);
        }

        .form-row { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }

        button {
            background: var(--primary);
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }
        button:hover { background: var(--primary-dark); }

        .ticket {
            border-top: 1px solid var(--border);
            padding: 20px 0;
        }
        .ticket:first-of-type { border-top: none; }
        .ticket h3 { margin: 0 0 6px; font-size: 17px; }
        .ticket p  { margin: 6px 0; }

        .meta { color: var(--muted); font-size: 13px; margin-top: 10px; }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            margin-right: 6px;
        }
        .priority-low    { background: #dcfce7; color: #166534; }
        .priority-medium { background: #fef9c3; color: #854d0e; }
        .priority-high   { background: #fee2e2; color: #991b1b; }

        .status-open        { background: #dbeafe; color: #1e40af; }
        .status-in_progress { background: #ffedd5; color: #9a3412; }
        .status-resolved    { background: #dcfce7; color: #166534; }

        .status-form {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
            margin-top: 14px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .error { color: #b91c1c; background: #fef2f2; }
        .error ul { margin: 0; padding-left: 20px; }

        .empty { color: var(--muted); text-align: center; padding: 20px 0; }

        .footer {
            text-align: center;
            color: var(--muted);
            font-size: 13px;
            padding-bottom: 30px;
        }
        .footer code {
            background: #e8ecf4;
            padding: 2px 8px;
            border-radius: 6px;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="header-inner">
        <h1>Support Desk</h1>
        <p>Manage support tickets in one place.</p>
    </div>
</div>

<div class="container">

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

            <div class="form-row">
                <label for="priority">Priority</label>
                <select id="priority" name="priority">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                </select>

                <button type="submit">Create ticket</button>
            </div>
        </form>
    </div>

    <div class="panel">
        <h2>Tickets ({{ $tickets->count() }})</h2>

        @forelse ($tickets as $ticket)
            <div class="ticket">
                <h3>#{{ $ticket->id }} — {{ $ticket->title }}</h3>

                <p>{{ $ticket->description }}</p>

                <p class="meta">
                    <span class="badge priority-{{ $ticket->priority }}">
                        {{ ucfirst($ticket->priority) }}
                    </span>
                    <span class="badge status-{{ $ticket->status }}">
                        {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                    </span>
                    Created: {{ $ticket->created_at->format('d M Y, H:i') }}
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
            <p class="empty">No tickets yet. Create your first ticket above.</p>
        @endforelse
    </div>

    <div class="footer">
        Served by pod: <code>{{ gethostname() }}</code>
    </div>

</div>
</body>
</html>
