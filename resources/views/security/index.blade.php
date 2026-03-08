@extends('layouts.app')


@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">System Security</h1>
        <p class="mb-0 text-muted">Monitor and manage system security</p>
    </div>
    <div>
        <a href="{{ route('security.settings') }}" class="btn btn-primary">
            <i class="fas fa-cog me-1"></i> Security Settings
        </a>
    </div>
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

<!-- Login Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Logins Today</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $loginStats['total_today'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-sign-in-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Successful</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $loginStats['successful_today'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Failed Attempts</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $loginStats['failed_today'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Blocked</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $loginStats['blocked_today'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-ban fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $userStats['total_users'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $userStats['active_users'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Locked Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $userStats['locked_users'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-lock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Administrators</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $userStats['admins'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Login Attempts -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Recent Login Attempts</h6>
                <a href="{{ route('security.login-history') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-primary">
                            <tr>
                                <th>Time</th>
                                <th>User/Email</th>
                                <th>IP Address</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLogins as $login)
                            <tr>
                                <td>
                                    <small>{{ $login->login_at->format('M d, H:i') }}</small>
                                </td>
                                <td>
                                    {{ $login->user->name ?? $login->email ?? 'Unknown' }}
                                </td>
                                <td><code>{{ $login->ip_address }}</code></td>
                                <td>{!! $login->status_badge !!}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No login attempts recorded</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Security Management</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('security.login-history') }}" class="btn btn-outline-primary">
                        <i class="fas fa-history me-1"></i> Login History
                    </a>
                    <a href="{{ route('security.active-sessions') }}" class="btn btn-outline-info">
                        <i class="fas fa-desktop me-1"></i> Active Sessions
                    </a>
                    <a href="{{ route('security.settings') }}" class="btn btn-outline-warning">
                        <i class="fas fa-cog me-1"></i> Security Settings
                    </a>
                    <a href="{{ route('security.export-logs') }}?type=login&format=csv" class="btn btn-outline-success">
                        <i class="fas fa-download me-1"></i> Export Login Logs
                    </a>
                </div>
            </div>
        </div>

        <!-- Current Security Settings -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Current Settings</h6>
            </div>
            <div class="card-body">
                @if(isset($settings['login']))
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-key text-warning me-1"></i>
                        Max Login Attempts: <strong>{{ $settings['login']['max_login_attempts'] ?? 5 }}</strong>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-clock text-info me-1"></i>
                        Lockout Duration: <strong>{{ $settings['login']['lockout_duration'] ?? 900 }} seconds</strong>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-hourglass-half text-primary me-1"></i>
                        Session Timeout: <strong>{{ $settings['session']['session_timeout'] ?? 120 }} min</strong>
                    </li>
                </ul>
                @else
                <p class="text-muted mb-0">Settings not configured</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
