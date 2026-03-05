@extends('layouts.app')


@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Active Sessions</h1>
        <p class="mb-0 text-muted">View and manage active user sessions</p>
    </div>
    <a href="{{ route('security.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

@if(Session::has('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ Session::get('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(Session::has('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ Session::get('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-desktop me-1"></i> Currently Active Sessions ({{ $sessions->count() }})
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>User</th>
                        <th>IP Address</th>
                        <th>Browser</th>
                        <th>Platform</th>
                        <th>Last Activity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                    <tr class="{{ $session->user_id == auth()->id() ? 'table-info' : '' }}">
                        <td>
                            <strong>{{ $session->user_name }}</strong>
                            <br><small class="text-muted">{{ $session->user_email }}</small>
                            @if($session->user_id == auth()->id())
                            <span class="badge bg-primary">Current</span>
                            @endif
                        </td>
                        <td><code>{{ $session->ip_address }}</code></td>
                        <td>
                            @if($session->browser == 'Chrome')
                            <i class="fab fa-chrome text-warning"></i>
                            @elseif($session->browser == 'Firefox')
                            <i class="fab fa-firefox text-warning"></i>
                            @elseif($session->browser == 'Safari')
                            <i class="fab fa-safari text-info"></i>
                            @elseif($session->browser == 'Edge')
                            <i class="fab fa-edge text-primary"></i>
                            @else
                            <i class="fas fa-globe"></i>
                            @endif
                            {{ $session->browser }}
                        </td>
                        <td>
                            @if($session->platform == 'Windows')
                            <i class="fab fa-windows text-info"></i>
                            @elseif($session->platform == 'Mac OS')
                            <i class="fab fa-apple"></i>
                            @elseif($session->platform == 'Linux')
                            <i class="fab fa-linux"></i>
                            @else
                            <i class="fas fa-desktop"></i>
                            @endif
                            {{ $session->platform }}
                        </td>
                        <td>
                            <small>{{ $session->last_activity->format('M d, Y H:i:s') }}</small>
                            <br><small class="text-muted">{{ $session->last_activity->diffForHumans() }}</small>
                        </td>
                        <td>
                            @if($session->user_id != auth()->id())
                            <form action="{{ route('security.session.terminate', $session->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" title="Terminate Session" data-confirm-delete="Are you sure you want to terminate this session?">
                                    <i class="fas fa-power-off"></i> Terminate
                                </button>
                            </form>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No active sessions found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="alert alert-info">
    <i class="fas fa-info-circle me-1"></i>
    <strong>Note:</strong> Sessions shown here are based on the database session driver. If file-based sessions are used, this list may be incomplete.
</div>
@endsection
