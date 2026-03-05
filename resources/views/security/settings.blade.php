@extends('layouts.app')

@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Security Settings</h1>
        <p class="mb-0 text-muted">Configure system security policies</p>
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

@if($errors->any())
<div class="alert alert-danger" role="alert">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('security.settings.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row">
        <!-- Password Policy -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-key me-1"></i> Password Policy
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="password_min_length" class="form-label">Minimum Password Length</label>
                        <input type="number" name="password_min_length" id="password_min_length" 
                               class="form-control" min="6" max="32"
                               value="{{ old('password_min_length', $settings['password']['password_min_length'] ?? 8) }}">
                        <small class="text-muted">Minimum characters required (6-32)</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="password_require_uppercase" 
                                   id="password_require_uppercase" value="1"
                                   {{ old('password_require_uppercase', $settings['password']['password_require_uppercase'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="password_require_uppercase">
                                Require uppercase letters (A-Z)
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="password_require_lowercase" 
                                   id="password_require_lowercase" value="1"
                                   {{ old('password_require_lowercase', $settings['password']['password_require_lowercase'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="password_require_lowercase">
                                Require lowercase letters (a-z)
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="password_require_numbers" 
                                   id="password_require_numbers" value="1"
                                   {{ old('password_require_numbers', $settings['password']['password_require_numbers'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="password_require_numbers">
                                Require numbers (0-9)
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="password_require_symbols" 
                                   id="password_require_symbols" value="1"
                                   {{ old('password_require_symbols', $settings['password']['password_require_symbols'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="password_require_symbols">
                                Require special symbols (!@#$%^&*)
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login Security -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-sign-in-alt me-1"></i> Login Security
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="max_login_attempts" class="form-label">Maximum Login Attempts</label>
                        <input type="number" name="max_login_attempts" id="max_login_attempts" 
                               class="form-control" min="3" max="10"
                               value="{{ old('max_login_attempts', $settings['login']['max_login_attempts'] ?? 5) }}">
                        <small class="text-muted">Failed attempts before account lockout (3-10)</small>
                    </div>

                    <div class="mb-3">
                        <label for="lockout_duration" class="form-label">Lockout Duration (minutes)</label>
                        <input type="number" name="lockout_duration" id="lockout_duration" 
                               class="form-control" min="5" max="60"
                               value="{{ old('lockout_duration', $settings['login']['lockout_duration'] ?? 15) }}">
                        <small class="text-muted">How long accounts stay locked (5-60 minutes)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Session Settings -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock me-1"></i> Session Settings
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="session_timeout" class="form-label">Session Timeout (minutes)</label>
                        <input type="number" name="session_timeout" id="session_timeout" 
                               class="form-control" min="15" max="480"
                               value="{{ old('session_timeout', $settings['session']['session_timeout'] ?? 120) }}">
                        <small class="text-muted">Idle time before automatic logout (15-480 minutes)</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="single_session" 
                                   id="single_session" value="1"
                                   {{ old('single_session', $settings['session']['single_session'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="single_session">
                                Allow only one active session per user
                            </label>
                        </div>
                        <small class="text-muted">New login will terminate existing sessions</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Audit Settings -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-1"></i> Audit Settings
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="audit_log_retention_days" class="form-label">Log Retention Period (days)</label>
                        <input type="number" name="audit_log_retention_days" id="audit_log_retention_days" 
                               class="form-control" min="30" max="365"
                               value="{{ old('audit_log_retention_days', $settings['audit']['audit_log_retention_days'] ?? 90) }}">
                        <small class="text-muted">How long to keep audit logs (30-365 days)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-1"></i> Save Settings
                </button>
            </div>
        </div>
    </div>
</form>
@endsection
