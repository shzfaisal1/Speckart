<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Custom Log Viewer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 4 CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container-fluid p-5">
        <h2 class="mb-4">📜 Custom Log Viewer</h2>

        <!-- Filter Form -->
        <form method="GET" action="{{ url('/custom-logs') }}" class="form-row align-items-end mb-4">
            <!-- Log Date -->
            <div class="form-group col-md-3">
                <label for="date">📅 Log Date</label>
                <input type="date" id="date" name="date" value="{{ $logDate }}" class="form-control">
            </div>

            <!-- Search -->
            <div class="form-group col-md-3">
                <label for="search">🔍 Search</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Search logs..." class="form-control">
            </div>

            <!-- Log Level -->
            <div class="form-group col-md-3">
                <label for="level">⚠️ Log Level</label>
                <select name="level" id="level" class="form-control">
                    <option value="">All Levels</option>
                    @foreach($levels as $lvl)
                    <option value="{{ $lvl }}" @if(request('level')==$lvl) selected @endif>{{ $lvl }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Submit -->
            <div class="form-group col-md-3">
                <button type="submit" class="btn btn-primary btn-block">Filter</button>
            </div>
        </form>

        <!-- Logs Display -->
        <div class="card mb-3">
            <div class="card-body" style="font-family: monospace; white-space: pre-wrap;">
                @forelse($logs as $log)
                <div style="border-bottom: 1px solid #e9ecef; padding: 4px 0;">{{ $log }}</div>
                @empty
                <p>No logs found for <strong>{{ $logDate }}</strong>.</p>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $logs->links() }}
        </div>
    </div>

    <!-- Bootstrap 4 JS dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>