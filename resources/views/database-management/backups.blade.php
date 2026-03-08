@extends('layouts.app')


@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Database Backups</h1>
        <p class="mb-0 text-muted">Manage your database backup files</p>
    </div>
    <div>
        <a href="{{ route('database.index') }}" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createBackupModal">
            <i class="fas fa-plus me-1"></i> Create Backup
        </button>
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

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Filename</th>
                        <th>Size</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backups as $backup)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <i class="fas fa-file-archive text-warning me-1"></i>
                            {{ Str::limit($backup->filename, 35) }}
                            @if($backup->notes)
                            <br><small class="text-muted">{{ Str::limit($backup->notes, 50) }}</small>
                            @endif
                        </td>
                        <td>{{ $backup->formatted_size }}</td>
                        <td>{!! $backup->type_badge !!}</td>
                        <td>{!! $backup->status_badge !!}</td>
                        <td>{{ $backup->creator->name ?? 'System' }}</td>
                        <td>{{ $backup->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                @if($backup->status === 'completed' && $backup->fileExists())
                                <a href="{{ route('database.backup.download', $backup->id) }}" class="btn btn-info" title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                                <a href="{{ route('database.backup.restore.show', $backup->id) }}" class="btn btn-warning" title="Restore">
                                    <i class="fas fa-undo"></i>
                                </a>
                                @endif
                                <form action="{{ route('database.backup.delete', $backup->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger" title="Delete" data-confirm-delete="Are you sure you want to delete this backup? This action cannot be undone.">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No backups found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $backups->links() }}
        </div>
    </div>
</div>

<!-- Create Backup Modal -->
<div class="modal fade" id="createBackupModal" tabindex="-1" aria-labelledby="createBackupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createBackupModalLabel">Create Database Backup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('database.backup.create') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i>
                        This will create a full backup of the current database. The process may take a few moments depending on the database size.
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (optional)</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" maxlength="500" placeholder="Add any notes about this backup..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-database me-1"></i> Create Backup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
