@extends('layouts.app')

@section('title', 'Restore Database')

@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Restore Database</h1>
        <p class="mb-0 text-muted">Restore database from backup</p>
    </div>
    <a href="{{ route('database.backups') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Backups
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-danger text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-exclamation-triangle me-1"></i> Warning: Database Restore
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-circle me-1"></i> This action is irreversible!</h5>
                    <p class="mb-0">Restoring this backup will <strong>replace all current data</strong> in the database with the data from the backup file. Any changes made after this backup was created will be permanently lost.</p>
                </div>

                <div class="card mb-4">
                    <div class="card-body bg-light">
                        <h6 class="font-weight-bold">Backup Information:</h6>
                        <table class="table table-sm mb-0">
                            <tr>
                                <td><strong>Filename:</strong></td>
                                <td>{{ $backup->filename }}</td>
                            </tr>
                            <tr>
                                <td><strong>Size:</strong></td>
                                <td>{{ $backup->formatted_size }}</td>
                            </tr>
                            <tr>
                                <td><strong>Created:</strong></td>
                                <td>{{ $backup->created_at->format('F d, Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Created By:</strong></td>
                                <td>{{ $backup->creator->name ?? 'System' }}</td>
                            </tr>
                            @if($backup->notes)
                            <tr>
                                <td><strong>Notes:</strong></td>
                                <td>{{ $backup->notes }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <form action="{{ route('database.backup.restore', $backup->id) }}" method="POST" id="restoreForm">
                    @csrf
                    <div class="mb-4">
                        <label for="confirmation" class="form-label">
                            <strong>To confirm, type "RESTORE" in the box below:</strong>
                        </label>
                        <input type="text" name="confirmation" id="confirmation" class="form-control @error('confirmation') is-invalid @enderror" 
                               placeholder="Type RESTORE to confirm" required autocomplete="off">
                        @error('confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('database.backups') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-danger" id="restoreBtn" disabled>
                            <i class="fas fa-undo me-1"></i> Restore Database
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('confirmation').addEventListener('input', function() {
    const btn = document.getElementById('restoreBtn');
    btn.disabled = this.value !== 'RESTORE';
});

document.getElementById('restoreForm').addEventListener('submit', function(e) {
    if (!confirm('Are you absolutely sure you want to restore this backup? This will replace ALL current data!')) {
        e.preventDefault();
    }
});
</script>
@endpush
