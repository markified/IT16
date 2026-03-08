@extends('layouts.app')


@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Database Management</h1>
        <p class="mb-0 text-muted">Monitor and manage your database</p>
    </div>
    <div>
        <a href="{{ route('database.backups') }}" class="btn btn-primary">
            <i class="fas fa-database me-1"></i> Manage Backups
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

<!-- Overview Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Database</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $databaseName }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-database fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Tables</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ count($tables) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-table fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Size</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSizeFormatted }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hdd fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Rows</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalRows) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Database Tables -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Database Tables</h6>
                <form action="{{ route('database.optimize') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="button" class="btn btn-sm btn-warning" data-confirm="This will optimize all tables. Are you sure you want to continue?">
                        <i class="fas fa-wrench me-1"></i> Optimize All
                    </button>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>Table Name</th>
                                <th>Rows</th>
                                <th>Size</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tables as $table)
                            <tr>
                                <td>
                                    <i class="fas fa-table text-primary me-2"></i>
                                    {{ $table['name'] }}
                                </td>
                                <td>{{ $table['rows'] }}</td>
                                <td>{{ $table['size'] }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('database.table.show', $table['name']) }}" class="btn btn-info" title="View">
                                            <i class="fas fa-list"></i>
                                        </a>
                                        <a href="{{ route('database.table.export', ['table' => $table['name'], 'format' => 'csv']) }}" class="btn btn-success" title="Export CSV">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No tables found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Backups -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Recent Backups</h6>
                <a href="{{ route('database.backups') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @forelse($recentBackups as $backup)
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <small class="text-muted d-block">{{ $backup->created_at->format('M d, Y H:i') }}</small>
                        <span class="font-weight-bold">{{ Str::limit($backup->filename, 25) }}</span>
                        <br>
                        <small>{!! $backup->status_badge !!} {{ $backup->formatted_size }}</small>
                    </div>
                    @if($backup->status === 'completed' && $backup->fileExists())
                    <a href="{{ route('database.backup.download', $backup->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-download"></i>
                    </a>
                    @endif
                </div>
                @empty
                <p class="text-center text-muted mb-0">No backups found</p>
                @endforelse

                <div class="mt-3">
                    <form action="{{ route('database.backup.create') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block w-100">
                            <i class="fas fa-plus me-1"></i> Create New Backup
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('database.backups') }}" class="btn btn-outline-primary">
                        <i class="fas fa-database me-1"></i> Manage Backups
                    </a>
                    <form action="{{ route('database.optimize') }}" method="POST">
                        @csrf
                        <button type="button" class="btn btn-outline-warning w-100" data-confirm="This will optimize all tables. Are you sure you want to continue?">
                            <i class="fas fa-wrench me-1"></i> Optimize Database
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Helper function to format bytes (in case needed client-side)
function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}
</script>
@endpush
