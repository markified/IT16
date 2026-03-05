@extends('layouts.app')


@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Login History</h1>
        <p class="mb-0 text-muted">View all login attempts</p>
    </div>
    <div>
        <a href="{{ route('security.index') }}" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <a href="{{ route('security.export-logs') }}?type=login&format=csv" class="btn btn-success">
            <i class="fas fa-download me-1"></i> Export CSV
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('security.login-history') }}">
            <div class="row">
                <div class="col-md-2 mb-2">
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <select name="user_id" class="form-control">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <input type="text" name="ip_address" class="form-control" value="{{ request('ip_address') }}" placeholder="IP Address">
                </div>
                <div class="col-md-2 mb-2">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="From">
                </div>
                <div class="col-md-2 mb-2">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="To">
                </div>
                <div class="col-md-2 mb-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Login History Table -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>Date/Time</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>IP Address</th>
                        <th>Browser</th>
                        <th>Platform</th>
                        <th>Status</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loginHistories as $history)
                    <tr>
                        <td>
                            <small>{{ $history->login_at->format('Y-m-d') }}</small><br>
                            <small class="text-muted">{{ $history->login_at->format('H:i:s') }}</small>
                        </td>
                        <td>{{ $history->user->name ?? 'N/A' }}</td>
                        <td>{{ $history->email ?? 'N/A' }}</td>
                        <td><code>{{ $history->ip_address }}</code></td>
                        <td>
                            <small>
                                @if($history->browser == 'Chrome')
                                <i class="fab fa-chrome text-warning"></i>
                                @elseif($history->browser == 'Firefox')
                                <i class="fab fa-firefox text-warning"></i>
                                @elseif($history->browser == 'Safari')
                                <i class="fab fa-safari text-info"></i>
                                @elseif($history->browser == 'Edge')
                                <i class="fab fa-edge text-primary"></i>
                                @else
                                <i class="fas fa-globe"></i>
                                @endif
                                {{ $history->browser }}
                            </small>
                        </td>
                        <td>
                            <small>
                                @if($history->platform == 'Windows')
                                <i class="fab fa-windows text-info"></i>
                                @elseif($history->platform == 'Mac OS')
                                <i class="fab fa-apple"></i>
                                @elseif($history->platform == 'Linux')
                                <i class="fab fa-linux"></i>
                                @elseif($history->platform == 'Android')
                                <i class="fab fa-android text-success"></i>
                                @elseif($history->platform == 'iOS')
                                <i class="fab fa-apple"></i>
                                @else
                                <i class="fas fa-desktop"></i>
                                @endif
                                {{ $history->platform }}
                            </small>
                        </td>
                        <td>{!! $history->status_badge !!}</td>
                        <td><small>{{ $history->failure_reason ?? '-' }}</small></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No login history found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $loginHistories->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
