@extends('layouts.app')

@section('contents')
<h1 class="mb-0">Profile</h1>
<hr />

@if(Session::has('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ Session::get('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(Session::has('warning'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    {{ Session::get('warning') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(Session::has('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ Session::get('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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

<div class="row">
    <!-- Profile Settings -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user me-1"></i> Profile Settings
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control" 
                               value="{{ old('name', auth()->user()->name) }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" class="form-control" 
                               value="{{ auth()->user()->email }}" disabled>
                        <small class="text-muted">Email cannot be changed</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control" 
                               value="{{ ucfirst(auth()->user()->role) }}" disabled>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Last Login</label>
                        <input type="text" class="form-control" 
                               value="{{ auth()->user()->last_login_at ? auth()->user()->last_login_at->format('M d, Y h:i A') : 'Never' }}" disabled>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Change Password -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-key me-1"></i> Change Password
                </h6>
            </div>
            <div class="card-body">
                @if(auth()->user()->force_password_change)
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <strong>Password Change Required:</strong> An administrator has requested that you change your password.
                </div>
                @endif
                
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" name="current_password" id="current_password" 
                               class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" name="password" id="password" 
                               class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                               class="form-control" required>
                    </div>
                    
                    @if(isset($passwordRequirements) && count($passwordRequirements) > 0)
                    <div class="mb-3">
                        <small class="text-muted">
                            <strong>Password Requirements:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach($passwordRequirements as $requirement)
                                <li>{{ $requirement }}</li>
                                @endforeach
                            </ul>
                        </small>
                    </div>
                    @endif
                    
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-key me-1"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Account Information -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-1"></i> Account Information
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <p><strong>Account Status:</strong> 
                            @if(auth()->user()->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Account Created:</strong> 
                            {{ auth()->user()->created_at->format('M d, Y') }}
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Password Last Changed:</strong> 
                            {{ auth()->user()->password_changed_at ? auth()->user()->password_changed_at->format('M d, Y h:i A') : 'Never' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection