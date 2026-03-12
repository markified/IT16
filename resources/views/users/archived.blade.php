@extends('layouts.app')

@section('contents')
<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="mb-0">Archived Users</h1>
        <p class="mb-0 text-muted">{{ $users->count() }} archived users</p>
    </div>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Active Users
    </a>
</div>
<hr />

@if(Session::has('success'))
<div class="alert alert-success" role="alert">
    {{ Session::get('success') }}
</div>
@endif

@if(Session::has('error'))
<div class="alert alert-danger" role="alert">
    {{ Session::get('error') }}
</div>
@endif

<table class="table table-hover">
    <thead class="table-secondary">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Archived At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($users as $user)
        <tr>
            <td class="align-middle">{{ $loop->iteration }}</td>
            <td class="align-middle">{{ $user->name }}</td>
            <td class="align-middle">@maskable($user->email, 'email')</td>
            <td class="align-middle">
                <span class="badge {{ $user->role === 'superadmin' ? 'bg-danger' : ($user->role === 'admin' ? 'bg-warning text-dark' : 'bg-info') }}">
                    {{ ucfirst($user->role) }}
                </span>
            </td>
            <td class="align-middle">{{ $user->updated_at->format('M d, Y') }}</td>
            <td class="align-middle">
                <div class="btn-group btn-group-sm">
                    <form action="{{ route('users.restore', $user->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success" title="Restore">
                            <i class="fas fa-undo me-1"></i> Restore
                        </button>
                    </form>
                    @if(Auth::user()->role === 'superadmin')
                    <form action="{{ route('users.permanent-delete', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to PERMANENTLY delete this user? This action cannot be undone!')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" title="Permanent Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center py-4">
                <i class="fas fa-archive fa-3x text-gray-300 mb-3"></i>
                <p class="text-muted mb-0">No archived users found</p>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
