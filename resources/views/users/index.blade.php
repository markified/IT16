@extends('layouts.app')

@section('contents')
<div class="d-flex align-items-center justify-content-between">
    <h1 class="mb-0">Manage Users</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('users.archived') }}" class="btn btn-secondary">
            <i class="fas fa-archive me-1"></i> View Archived
        </a>
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" onclick="$('#addUserModal').modal('show')">
            Add New User
        </button>
    </div>
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
@if($errors->any())
<div class="alert alert-danger" role="alert">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<table class="table table-hover">
    <thead class="table-primary">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if($users->count() > 0)
        @foreach($users as $user)
        <tr class="{{ !$user->is_approved ? 'table-warning' : '' }}">
            <td class="align-middle">{{ $loop->iteration }}</td>
            <td class="align-middle">{{ $user->name }}</td>
            <td class="align-middle">@maskable($user->email, 'email')</td>
            <td class="align-middle">
                <span class="badge {{ $user->role === 'superadmin' ? 'bg-danger' : ($user->role === 'admin' ? 'bg-warning text-dark' : 'bg-info') }}">
                    {{ ucfirst($user->role) }}
                </span>
            </td>
            <td class="align-middle">
                @if($user->is_approved)
                <span class="badge bg-success">Approved</span>
                @else
                <span class="badge bg-warning text-dark">Pending Approval</span>
                @endif
            </td>
            <td class="align-middle">{{ $user->created_at->format('M d, Y') }}</td>
            <td class="align-middle">
                <div class="btn-group" role="group">
                    @if(!$user->is_approved && Auth::user()->role === 'superadmin')
                    <form action="{{ route('users.approve', $user->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success" title="Approve">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                    @endif
                    <button type="button" class="btn btn-warning" onclick="$('#editUserModal{{ $user->id }}').modal('show')">
                        Edit
                    </button>
                    @if($user->id !== auth()->id() && !in_array($user->role, ['admin', 'superadmin']))
                        {{-- All users need password confirmation to archive --}}
                        <button type="button" class="btn btn-secondary" onclick="openArchiveModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->role }}')">
                            <i class="fas fa-archive me-1"></i> Archive
                        </button>
                    @endif
                </div>
            </td>
        </tr>

        <!-- Edit User Modal -->
        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                                    @if(Auth::user()->role === 'superadmin')
                                    <option value="superadmin" {{ $user->role === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    @endif
                                    <option value="inventory" {{ $user->role === 'inventory' ? 'selected' : '' }}>Inventory</option>
                                    <option value="security" {{ $user->role === 'security' ? 'selected' : '' }}>Security</option>
                                </select>
                                @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password (leave blank to keep current)</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if(isset($passwordRequirements) && count($passwordRequirements) > 0)
                                <small class="text-muted">
                                    <strong>Password Requirements:</strong>
                                    <ul class="mb-0 mt-1">
                                        @foreach($passwordRequirements as $requirement)
                                        <li>{{ $requirement }}</li>
                                        @endforeach
                                    </ul>
                                </small>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-warning">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
        @else
        <tr>
            <td class="text-center" colspan="7">No users found</td>
        </tr>
        @endif
    </tbody>
</table>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn btn-light border-0" onclick="$('#addUserModal').modal('hide')" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select id="role" name="role" class="form-control @error('role') is-invalid @enderror" required onchange="showAddUserApprovalNote()">
                            @if(Auth::user()->role === 'superadmin')
                            <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            @endif
                            <option value="inventory" {{ old('role') === 'inventory' ? 'selected' : '' }}>Inventory (Requires Approval)</option>
                            <option value="security" {{ old('role') === 'security' ? 'selected' : '' }}>Security (Requires Approval)</option>
                        </select>
                        @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="addUserApprovalNote" class="alert alert-warning mt-2 py-2" style="display: none;">
                            <small><i class="fas fa-exclamation-triangle me-1"></i> This role requires approval before the user can log in.</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if(isset($passwordRequirements) && count($passwordRequirements) > 0)
                        <small class="text-muted">
                            <strong>Password Requirements:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach($passwordRequirements as $requirement)
                                <li>{{ $requirement }}</li>
                                @endforeach
                            </ul>
                        </small>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include Bootstrap CSS -->
<link href="{{ asset('admin_assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

<!-- Include Bootstrap JS -->
<script src="{{ asset('admin_assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

@if($errors->any())
<script>
    $(document).ready(function() {
        $('#addUserModal').modal('show');
    });
</script>
@endif

<!-- Archive User with Password Confirmation Modal -->
<div class="modal fade" id="archiveUserModal" tabindex="-1" aria-labelledby="archiveUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="archiveUserModalLabel">Confirm User Archive</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="archiveUserForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        You are about to archive user: <strong id="archiveUserName"></strong>
                        <br><small>Role: <span id="archiveUserRole" class="badge bg-primary"></span></small>
                    </div>
                    <p class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i> Password confirmation is required to archive user accounts.
                    </p>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Your Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        <small class="text-muted">Enter your current password to authorize this action.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-archive me-1"></i> Archive User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openArchiveModal(userId, userName, userRole) {
    document.getElementById('archiveUserName').textContent = userName;
    document.getElementById('archiveUserRole').textContent = userRole.charAt(0).toUpperCase() + userRole.slice(1);
    document.getElementById('archiveUserForm').action = '/users/' + userId;
    document.getElementById('confirm_password').value = '';
    $('#archiveUserModal').modal('show');
}

function showAddUserApprovalNote() {
    var role = document.getElementById('role').value;
    var note = document.getElementById('addUserApprovalNote');
    if (role === 'inventory' || role === 'security') {
        note.style.display = 'block';
    } else {
        note.style.display = 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    showAddUserApprovalNote();
});
</script>
@endsection
