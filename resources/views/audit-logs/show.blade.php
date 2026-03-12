@extends('layouts.app')

@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Audit Log Details</h1>
        <p class="mb-0 text-muted">Log ID: #{{ $log->id }}</p>
    </div>
    <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Log Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="40%"><strong>Timestamp:</strong></td>
                        <td>{{ $log->created_at->format('F d, Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <td><strong>User:</strong></td>
                        <td>{{ $log->user->name ?? 'System' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Action:</strong></td>
                        <td>{!! $log->action_badge !!}</td>
                    </tr>
                    <tr>
                        <td><strong>Resource Type:</strong></td>
                        <td><span class="badge bg-secondary">{{ $log->model_type }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Resource ID:</strong></td>
                        <td>#{{ $log->model_id }}</td>
                    </tr>
                    <tr>
                        <td><strong>IP Address:</strong></td>
                        <td>@maskable($log->ip_address, 'ip')</td>
                    </tr>
                    <tr>
                        <td><strong>User Agent:</strong></td>
                        <td><small class="text-muted">{{ Str::limit($log->user_agent, 80) }}</small></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        @if($log->description)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">Description</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $log->description }}</p>
            </div>
        </div>
        @endif

        @if($log->old_values || $log->new_values)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">Changes</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($log->old_values)
                    <div class="col-md-6">
                        <h6 class="text-danger"><i class="fas fa-minus-circle"></i> Old Values</h6>
                        <pre class="bg-light p-2 rounded"><code>{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</code></pre>
                    </div>
                    @endif
                    @if($log->new_values)
                    <div class="col-md-6">
                        <h6 class="text-success"><i class="fas fa-plus-circle"></i> New Values</h6>
                        <pre class="bg-light p-2 rounded"><code>{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</code></pre>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
