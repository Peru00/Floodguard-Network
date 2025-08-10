<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\VolunteerProfile;
use App\Models\DonorProfile;

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
            'password' => 'required',
            'role' => 'required|in:admin,volunteer,donor'
        ]);

        // Try to find user by email and role
        $user = User::where('email', $credentials['email'])
                   ->where('role', $credentials['role'])
                   ->first();

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
                return redirect()->route('dashboard')->with('message', 'Volunteer dashboard coming soon!');
            case 'admin':
                return redirect()->route('dashboard')->with('message', 'Admin dashboard coming soon!');
            default:
                return redirect()->route('dashboard');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home');
    }
}
