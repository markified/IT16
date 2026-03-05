<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LoginHistory;
use App\Models\SecuritySetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;
use App\Models\AuditLog;

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

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('login')->with('success', 'Your account has been created successfully. You can now log in.');
    }

    public function login()
    {
        // Generate CAPTCHA
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
        
        return view('auth/login', [
            'captcha_question' => "$num1 $operator $num2 = ?"
        ]);
    }

    public function loginAction(Request $request)
    {
        Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'captcha' => 'required|numeric'
        ])->validate();

        // Validate CAPTCHA
        $captchaAnswer = session('captcha_answer');
        if ((int)$request->captcha !== (int)$captchaAnswer) {
            throw ValidationException::withMessages([
                'captcha' => 'Incorrect CAPTCHA answer. Please try again.'
            ]);
        }

        // Check if user exists and is locked
        $user = User::where('email', $request->email)->first();
        
        if ($user) {
            // Check if account is locked
            if ($user->isLocked()) {
                LoginHistory::logBlocked($request->email, $request, 'Account locked');
                throw ValidationException::withMessages([
                    'email' => 'Your account is temporarily locked. Please try again later.'
                ]);
            }

            // Check if account is inactive
            if (!$user->is_active) {
                LoginHistory::logBlocked($request->email, $request, 'Account inactive');
                throw ValidationException::withMessages([
                    'email' => 'Your account has been deactivated. Please contact an administrator.'
                ]);
            }
        }

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            // Log failed attempt
            LoginHistory::logFailed($request->email, $request, 'Invalid credentials');
            
            // Increment failed attempts if user exists
            if ($user) {
                $user->incrementFailedAttempts();
            }

            AuditLog::logLoginFailed($request->email);
            throw ValidationException::withMessages([
                'email' => trans('auth.failed')
            ]);
        }

        // Successful login
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
