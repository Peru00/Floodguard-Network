<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\VolunteerProfile;
use App\Models\DonorProfile;
use App\Mail\PasswordResetMail;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showSignup()
    {
        return view('auth.signup');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Find user by email (auto-detect role)
        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Update last login
            $user->update(['last_login' => now()]);
            
            // Login the user
            Auth::login($user);
            
            // Redirect based on role
            return $this->redirectUserToRoleDashboard($user);
        }
        
        return back()->withErrors([
            'email' => 'Invalid email or password',
        ])->withInput($request->except('password'));
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|string|email|max:100|unique:users',
            'phone' => 'required|string|max:50',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:volunteer,donor',
            'location' => 'required_if:role,volunteer|string|max:100|nullable',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Generate unique user ID
        $userId = 'USER-' . time();

        // Create user
        $user = User::create([
            'user_id' => $userId,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $request->role,
            'registration_date' => now(),
            'status' => 'active'
        ]);

        // Create role-specific profile
        if ($request->role === 'volunteer') {
            VolunteerProfile::create([
                'volunteer_id' => $userId,
                'location' => $request->location,
                'is_available' => true,
                'people_helped' => 0
            ]);
        } elseif ($request->role === 'donor') {
            DonorProfile::create([
                'donor_id' => $userId,
                'donor_type' => 'individual',
                'total_donations' => 0,
                'total_amount' => 0.00
            ]);
        }

        // Login the user
        Auth::login($user);

        // Redirect based on role
        return $this->redirectUserToRoleDashboard($user);
    }

    public function dashboard()
    {
        $user = Auth::user();
        return $this->redirectUserToRoleDashboard($user);
    }
    
    private function redirectUserToRoleDashboard($user)
    {
        switch($user->role) {
            case 'donor':
                return redirect()->route('donor.dashboard');
            case 'volunteer':
                return redirect()->route('volunteer.dashboard');
            case 'admin':
                return redirect()->route('admin.dashboard');
            default:
                return redirect()->route('dashboard');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home');
    }

    // Forgot Password Methods
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        // Delete any existing tokens for this email
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Generate a unique token
        $token = Str::random(64);

        // Store the token in database
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => now()
        ]);

        // Create reset link
        $resetLink = url('/reset-password/' . $token . '?email=' . urlencode($request->email));

        // Get user info for personalization
        $user = User::where('email', $request->email)->first();

        try {
            // Check if we're using log driver (development mode)
            if (config('mail.default') === 'log') {
                // For development - show the link since emails go to log
                \Log::info('Password reset email would be sent to: ' . $request->email);
                \Log::info('Reset link: ' . $resetLink);
                
                return back()->with('status', 'Reset your password by this link: ' . $resetLink);
            }
            
            // Send password reset email
            Mail::to($request->email)->send(new PasswordResetMail($resetLink, $user));
            
            return back()->with('status', 'Password reset link has been sent to your email address! Please check your inbox and spam folder.');
            
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
            
            // For development, provide the reset link as fallback
            return back()->with('status', 'Reset your password by this link: ' . $resetLink);
        }
    }

    public function showResetPassword(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
            'token' => 'required'
        ]);

        // Find the token record
        $tokenRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$tokenRecord || !Hash::check($request->token, $tokenRecord->token)) {
            return back()->withErrors(['token' => 'Invalid or expired token']);
        }

        // Check if token is expired (24 hours)
        if (now()->diffInHours($tokenRecord->created_at) > 24) {
            return back()->withErrors(['token' => 'Token has expired']);
        }

        // Update user password
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Password has been reset successfully!');
    }
}
