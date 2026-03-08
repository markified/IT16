<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register()
    {
        return view('auth/register');
    }

    public function registerSave(Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ],
            'role' => 'required|in:inventory,security',
        ])->validate();

        // All self-registered accounts require approval
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_approved' => false,
        ]);

        return redirect()->route('login')->with('info', 'Your account has been created but requires approval from an administrator. You will be notified once approved.');
    }

    public function login()
    {
        // Check if there was a failed login attempt
        $failedAttempts = session('failed_login_attempts', 0);
        $showCaptcha = $failedAttempts > 0;

        $captchaQuestion = null;

        // Generate CAPTCHA only if there was a failed attempt
        if ($showCaptcha) {
            $num1 = rand(1, 10);
            $num2 = rand(1, 10);
            $operators = ['+', '-', 'x'];
            $operator = $operators[array_rand($operators)];

            switch ($operator) {
                case '+':
                    $answer = $num1 + $num2;
                    break;
                case '-':
                    // Ensure positive result
                    if ($num1 < $num2) {
                        $temp = $num1;
                        $num1 = $num2;
                        $num2 = $temp;
                    }
                    $answer = $num1 - $num2;
                    break;
                case 'x':
                    $answer = $num1 * $num2;
                    break;
            }

            session(['captcha_answer' => $answer]);
            $captchaQuestion = "$num1 $operator $num2 = ?";
        }

        return view('auth/login', [
            'captcha_question' => $captchaQuestion,
            'show_captcha' => $showCaptcha,
        ]);
    }

    public function loginAction(Request $request)
    {
        // Check if CAPTCHA is required
        $failedAttempts = session('failed_login_attempts', 0);
        $requireCaptcha = $failedAttempts > 0;

        $validationRules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        // Only require CAPTCHA after first failed attempt
        if ($requireCaptcha) {
            $validationRules['captcha'] = 'required|numeric';
        }

        Validator::make($request->all(), $validationRules)->validate();

        // Validate CAPTCHA only if required
        if ($requireCaptcha) {
            $captchaAnswer = session('captcha_answer');
            if ((int) $request->captcha !== (int) $captchaAnswer) {
                throw ValidationException::withMessages([
                    'captcha' => 'Incorrect CAPTCHA answer. Please try again.',
                ]);
            }
        }

        // Check if user exists and is locked
        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Check if account is locked
            if ($user->isLocked()) {
                LoginHistory::logBlocked($request->email, $request, 'Account locked');
                throw ValidationException::withMessages([
                    'email' => 'Your account is temporarily locked. Please try again later.',
                ]);
            }

            // Check if account is inactive
            if (! $user->is_active) {
                LoginHistory::logBlocked($request->email, $request, 'Account inactive');
                throw ValidationException::withMessages([
                    'email' => 'Your account has been deactivated. Please contact an administrator.',
                ]);
            }

            // Check if account requires approval (for admin/superadmin roles)
            if (! $user->is_approved) {
                LoginHistory::logBlocked($request->email, $request, 'Account pending approval');
                throw ValidationException::withMessages([
                    'email' => 'Your account is pending approval. Please wait for an administrator to approve your account.',
                ]);
            }
        }

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            // Log failed attempt
            LoginHistory::logFailed($request->email, $request, 'Invalid credentials');

            // Increment failed login attempts in session
            session(['failed_login_attempts' => $failedAttempts + 1]);

            // Increment failed attempts if user exists
            if ($user) {
                $user->incrementFailedAttempts();
            }

            AuditLog::logLoginFailed($request->email);
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Successful login - reset failed attempts
        session()->forget('failed_login_attempts');
        $request->session()->regenerate();

        // Log successful login
        LoginHistory::logSuccess(Auth::user(), $request);
        Auth::user()->recordLogin($request);

        AuditLog::logLogin(Auth::user());

        // Check if password change is required
        if (Auth::user()->force_password_change) {
            return redirect()->route('profile')->with('warning', 'You are required to change your password.');
        }

        // Redirect based on role
        if (Auth::user()->isSecurity()) {
            return redirect()->route('security.index');
        }

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            // Update login history with logout time
            $lastLogin = LoginHistory::where('user_id', $user->id)
                ->whereNull('logout_at')
                ->latest('login_at')
                ->first();

            if ($lastLogin) {
                $lastLogin->update(['logout_at' => now()]);
            }

            AuditLog::logLogout($user);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        return redirect('/');
    }

    public function profile()
    {
        return view('profile');
    }
}
