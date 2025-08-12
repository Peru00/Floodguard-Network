<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Donation;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Admin access required');
        }
        
        try {
            // Get dashboard statistics
            $stats = [
                'volunteers' => User::where('role', 'volunteer')->count(),
                'donors' => User::where('role', 'donor')->count(),
                'total_donations' => Donation::where('status', 'approved')->sum('amount') ?? 0,
                'distributions' => DB::table('inventory')->sum('quantity') ?? 0,
                'locations' => DB::table('victims')->distinct('location')->count('location') ?? 0,
                'victims' => DB::table('victims')->count(),
                'pending_victims' => DB::table('victims')->where('status', 'pending')->count(),
                'assisted_victims' => DB::table('victims')->where('status', 'assisted')->count(),
                'high_priority_victims' => DB::table('victims')->where('priority', 'high')->count(),
                'pending_donations' => Donation::where('status', 'pending')->count()
            ];

            // Get pending donations for approval
            $pendingDonations = Donation::with('user')
                ->where('status', 'pending')
                ->orderBy('donation_date', 'desc')
                ->get();

            // Get volunteers with availability status - SIMPLIFIED
            $volunteers = User::where('role', 'volunteer')
                ->leftJoin('volunteer_profiles', 'users.user_id', '=', 'volunteer_profiles.volunteer_id')
                ->select(
                    'users.user_id',
                    'users.first_name', 
                    'users.last_name',
                    'users.phone',
                    'volunteer_profiles.is_available'
                )
                ->orderBy('volunteer_profiles.is_available', 'desc')
                ->get();

            // Get basic victims data - UPDATED to show actual data
            $victims = DB::table('victims')
                ->select('victim_id', 'name', 'location', 'needs', 'status', 'priority', 'family_size', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();

            return view('admin.dashboard', compact('stats', 'pendingDonations', 'volunteers', 'victims'));
            
        } catch (\Exception $e) {
            \Log::error('Error in admin dashboard', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Error loading dashboard: ' . $e->getMessage());
        }
    }

    public function updateDonationStatus(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Admin access required');
        }

        $request->validate([
            'donation_id' => 'required|exists:donations,donation_id',
            'action' => 'required|in:approve,reject'
        ]);

        try {
            DB::beginTransaction();

            $donation = Donation::where('donation_id', $request->donation_id)->first();
            
            $status = $request->action === 'approve' ? 'approved' : 'rejected';
            $donation->update([
                'status' => $status,
                'approved_by' => $user->user_id,
                'approved_at' => now()
            ]);

            // If approved and not monetary, add to inventory
            if ($request->action === 'approve' && $donation->donation_type !== 'monetary') {
                $inventoryId = 'INV-' . time();
                DB::table('inventory')->insert([
                    'inventory_id' => $inventoryId,
                    'item_type' => $donation->donation_type,
                    'quantity' => $donation->quantity,
                    'item_name' => $donation->items,
                    'source_donation_id' => $donation->donation_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return redirect()->route('admin.dashboard')
                ->with('success', 'Donation status updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating donation status', ['error' => $e->getMessage()]);
            return redirect()->route('admin.dashboard')
                ->with('error', 'Error updating donation status: ' . $e->getMessage());
        }
    }

    public function addVictim(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Admin access required');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'family_size' => 'required|integer|min:1',
            'phone' => 'nullable|string|max:15',
            'location' => 'required|string|max:100',
            'priority' => 'required|in:high,medium,low',
            'needs' => 'required|string|max:255'
        ]);

        try {
            $victimId = 'VIC-' . time();
            
            DB::table('victims')->insert([
                'victim_id' => $victimId,
                'name' => $request->name,
                'family_size' => $request->family_size,
                'phone' => $request->phone,
                'location' => $request->location,
                'priority' => $request->priority,
                'needs' => $request->needs,
                'status' => 'pending',
                'registration_date' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect()->route('admin.dashboard')
                ->with('success', 'Victim registered successfully with ID: ' . $victimId);

        } catch (\Exception $e) {
            \Log::error('Error adding victim', ['error' => $e->getMessage()]);
            return redirect()->route('admin.dashboard')
                ->with('error', 'Error registering victim: ' . $e->getMessage());
        }
    }

    public function addVolunteer(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Admin access required');
        }

        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15',
            'location' => 'required|string|max:100',
            'skill_type' => 'required|string|max:100'
        ]);

        try {
            DB::beginTransaction();

            // Create user account
            $userId = 'USER-VOL-' . str_pad(User::where('role', 'volunteer')->count() + 1, 3, '0', STR_PAD_LEFT);
            
            $userRecord = User::create([
                'user_id' => $userId,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt('volunteer123'), // Default password
                'role' => 'volunteer'
            ]);

            // Create volunteer profile
            DB::table('volunteer_profiles')->insert([
                'volunteer_id' => $userId,
                'location' => $request->location,
                'skill_type' => $request->skill_type,
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return redirect()->route('admin.dashboard')
                ->with('success', 'Volunteer registered successfully! ID: ' . $userId . ' | Default password: volunteer123');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error adding volunteer', ['error' => $e->getMessage()]);
            return redirect()->route('admin.dashboard')
                ->with('error', 'Error registering volunteer: ' . $e->getMessage());
        }
    }

    public function userManagement()
    {
        // Double-check admin access
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Admin access required.');
        }

        // Get all users with their roles
        $users = User::select('user_id', 'first_name', 'last_name', 'email', 'phone', 'role', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get role statistics
        $roleStats = [
            'admin' => User::where('role', 'admin')->count(),
            'donor' => User::where('role', 'donor')->count(),
            'volunteer' => User::where('role', 'volunteer')->count(),
        ];

        // Get recent victims
        $victims = DB::table('victims')
            ->select('victim_id', 'name', 'family_size', 'location', 'priority', 'status', 'registration_date')
            ->orderBy('registration_date', 'desc')
            ->limit(10)
            ->get();

        return view('admin.user-management', compact('users', 'roleStats', 'victims'));
    }

    public function createUser(Request $request)
    {
        // Double-check admin access
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Admin access required.');
        }

        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15',
            'role' => 'required|in:admin,donor,volunteer',
            'password' => 'required|string|min:6',
        ]);

        try {
            DB::beginTransaction();

            // Generate user ID based on role
            $rolePrefix = [
                'admin' => 'USER-ADM-',
                'donor' => 'USER-DNR-',
                'volunteer' => 'USER-VOL-'
            ];
            
            $lastUserCount = User::where('role', $request->role)->count();
            $userId = $rolePrefix[$request->role] . str_pad($lastUserCount + 1, 3, '0', STR_PAD_LEFT);

            // Create user account
            $user = User::create([
                'user_id' => $userId,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role,
                'password' => Hash::make($request->password),
                'registration_date' => now(),
                'status' => 'active',
            ]);

            // Create role-specific profile
            if ($request->role === 'donor') {
                DB::table('donor_profiles')->insert([
                    'user_id' => $user->user_id,
                    'location' => $request->location ?? '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } elseif ($request->role === 'volunteer') {
                DB::table('volunteer_profiles')->insert([
                    'user_id' => $user->user_id,
                    'location' => $request->location ?? '',
                    'skill_type' => $request->skill_type ?? 'General Support',
                    'is_available' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('admin.user-management')
                ->with('success', ucfirst($request->role) . ' account created successfully! User ID: ' . $userId);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating user', ['error' => $e->getMessage()]);
            return redirect()->route('admin.user-management')
                ->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }

    public function transferAdmin(Request $request)
    {
        // Double-check admin access
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Admin access required.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'current_password' => 'required|string',
        ]);

        try {
            // Verify current admin password
            if (!Hash::check($request->current_password, auth()->user()->password)) {
                return redirect()->route('admin.user-management')
                    ->with('error', 'Current password is incorrect.');
            }

            // Get target user
            $targetUser = User::where('user_id', $request->user_id)->firstOrFail();
            
            // Prevent self-transfer
            if ($targetUser->user_id === auth()->user()->user_id) {
                return redirect()->route('admin.user-management')
                    ->with('error', 'You cannot transfer admin rights to yourself.');
            }

            DB::beginTransaction();

            // Change current admin to regular user (donor by default)
            $currentAdmin = auth()->user();
            $currentAdmin->role = 'donor';
            $currentAdmin->save();

            // Create donor profile for ex-admin if not exists
            if (!DB::table('donor_profiles')->where('user_id', $currentAdmin->user_id)->exists()) {
                DB::table('donor_profiles')->insert([
                    'user_id' => $currentAdmin->user_id,
                    'location' => '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Make target user admin
            $targetUser->role = 'admin';
            $targetUser->save();

            DB::commit();

            // Log out current user since they're no longer admin
            auth()->logout();

            return redirect()->route('login')
                ->with('success', 'Admin rights transferred successfully to ' . $targetUser->first_name . ' ' . $targetUser->last_name);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error transferring admin', ['error' => $e->getMessage()]);
            return redirect()->route('admin.user-management')
                ->with('error', 'Error transferring admin rights: ' . $e->getMessage());
        }
    }

    public function editUser($id)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Admin access required');
        }

        try {
            $userToEdit = User::findOrFail($id);
            
            // Prevent admin from editing themselves
            if ($userToEdit->user_id === $user->user_id) {
                return redirect()->route('admin.user-management')
                    ->with('error', 'You cannot edit your own account');
            }

            return view('admin.edit-user', compact('userToEdit'));

        } catch (\Exception $e) {
            \Log::error('Error loading edit user page', ['error' => $e->getMessage(), 'user_id' => $id]);
            return redirect()->route('admin.user-management')
                ->with('error', 'User not found');
        }
    }

    public function updateUser(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Admin access required');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . ',user_id',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,donor,volunteer,victim',
            'password' => 'nullable|min:8|confirmed'
        ]);

        try {
            DB::beginTransaction();

            $userToUpdate = User::findOrFail($id);
            
            // Prevent admin from editing themselves
            if ($userToUpdate->user_id === $user->user_id) {
                return redirect()->route('admin.user-management')
                    ->with('error', 'You cannot edit your own account');
            }

            // Update user data
            $userToUpdate->first_name = $request->first_name;
            $userToUpdate->last_name = $request->last_name;
            $userToUpdate->email = $request->email;
            $userToUpdate->phone = $request->phone;
            $userToUpdate->role = $request->role;

            if ($request->filled('password')) {
                $userToUpdate->password = Hash::make($request->password);
            }

            $userToUpdate->save();

            DB::commit();

            return redirect()->route('admin.user-management')
                ->with('success', 'User updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating user', ['error' => $e->getMessage(), 'user_id' => $id]);
            return redirect()->route('admin.user-management')
                ->with('error', 'Error updating user: ' . $e->getMessage());
        }
    }

    public function deleteUser($id)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Admin access required');
        }

        try {
            DB::beginTransaction();

            $userToDelete = User::findOrFail($id);
            
            // Prevent admin from deleting themselves
            if ($userToDelete->user_id === $user->user_id) {
                return redirect()->route('admin.user-management')
                    ->with('error', 'You cannot delete your own account');
            }

            // Prevent deletion of the last admin
            if ($userToDelete->role === 'admin') {
                $adminCount = User::where('role', 'admin')->count();
                if ($adminCount <= 1) {
                    return redirect()->route('admin.user-management')
                        ->with('error', 'Cannot delete the last admin user');
                }
            }

            // Delete related profile data first
            if ($userToDelete->role === 'donor') {
                DB::table('donor_profiles')->where('donor_id', $userToDelete->user_id)->delete();
            } elseif ($userToDelete->role === 'volunteer') {
                DB::table('volunteer_profiles')->where('volunteer_id', $userToDelete->user_id)->delete();
            }

            // Delete the user
            $userName = $userToDelete->first_name . ' ' . $userToDelete->last_name;
            $userToDelete->delete();

            DB::commit();

            return redirect()->route('admin.user-management')
                ->with('success', 'User "' . $userName . '" has been deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting user', ['error' => $e->getMessage(), 'user_id' => $id]);
            return redirect()->route('admin.user-management')
                ->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }
}
