<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Donation;
use App\Models\ReliefCamp;
use App\Models\ChatMessage;
use App\Models\VolunteerTask;

class AdminController extends Controller
{
    /**
     * Generate a unique inventory ID that fits within 20 characters
     */
    private function generateUniqueInventoryId()
    {
        do {
            $inventoryId = 'INV-' . sprintf('%08d', mt_rand(10000000, 99999999));
        } while (DB::table('inventory')->where('inventory_id', $inventoryId)->exists());
        
        return $inventoryId;
    }

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
            if ($request->action === 'approve' && $donation->donation_type !== 'monetary' && $donation->donation_type !== 'money') {
                $inventoryId = $this->generateUniqueInventoryId();
                
                // Map donation_type to inventory item_type
                $itemType = $donation->donation_type;
                if ($itemType === 'medicine') {
                    $itemType = 'medical';
                }
                
                // Make sure we have valid values for quantity and items
                $quantity = !empty($donation->quantity) ? $donation->quantity : 1;
                $itemName = !empty($donation->items) ? $donation->items : $donation->donation_type;
                
                DB::table('inventory')->insert([
                    'inventory_id' => $inventoryId,
                    'item_type' => $itemType,
                    'quantity' => $quantity,
                    'item_name' => $itemName,
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

    public function reliefCamps()
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Admin access required');
        }

        try {
            // Get all relief camps with their managers
            $camps = ReliefCamp::with('manager')
                ->orderBy('location')
                ->get();

            // Get available volunteers for camp assignment
            $volunteers = User::where('role', 'volunteer')
                ->select('user_id', 'first_name', 'last_name')
                ->orderBy('first_name')
                ->get();

            // Calculate summary statistics
            $stats = [
                'total_camps' => $camps->count(),
                'total_capacity' => $camps->sum('capacity'),
                'total_occupancy' => $camps->sum('current_occupancy'),
                'available_spaces' => $camps->sum('capacity') - $camps->sum('current_occupancy'),
                'full_camps' => $camps->where('occupancy_status', 'full')->count(),
                'almost_full_camps' => $camps->where('occupancy_status', 'almost-full')->count(),
            ];

            return view('admin.relief-camps', compact('camps', 'volunteers', 'stats'));

        } catch (\Exception $e) {
            \Log::error('Error loading relief camps page', ['error' => $e->getMessage()]);
            return redirect()->route('admin.dashboard')
                ->with('error', 'Error loading relief camps: ' . $e->getMessage());
        }
    }

    public function createReliefCamp(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Admin access required');
        }

        $request->validate([
            'camp_name' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:10000',
            'managed_by' => 'nullable|exists:users,user_id'
        ]);

        try {
            DB::beginTransaction();

            $camp = ReliefCamp::create([
                'camp_id' => 'CAMP-' . time(),
                'camp_name' => $request->camp_name,
                'location' => $request->location,
                'capacity' => $request->capacity,
                'current_occupancy' => 0,
                'managed_by' => $request->managed_by ?: null,
            ]);

            DB::commit();

            return redirect()->route('admin.relief-camps')
                ->with('success', 'Relief camp "' . $camp->camp_name . '" created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating relief camp', ['error' => $e->getMessage()]);
            return redirect()->route('admin.relief-camps')
                ->with('error', 'Error creating relief camp: ' . $e->getMessage());
        }
    }

    public function updateReliefCamp(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Admin access required');
        }

        $request->validate([
            'camp_name' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:10000',
            'current_occupancy' => 'required|integer|min:0',
            'managed_by' => 'nullable|exists:users,user_id'
        ]);

        try {
            DB::beginTransaction();

            $camp = ReliefCamp::findOrFail($id);

            // Validate that current occupancy doesn't exceed capacity
            if ($request->current_occupancy > $request->capacity) {
                return redirect()->route('admin.relief-camps')
                    ->with('error', 'Current occupancy cannot exceed camp capacity');
            }

            $camp->update([
                'camp_name' => $request->camp_name,
                'location' => $request->location,
                'capacity' => $request->capacity,
                'current_occupancy' => $request->current_occupancy,
                'managed_by' => $request->managed_by ?: null,
            ]);

            DB::commit();

            return redirect()->route('admin.relief-camps')
                ->with('success', 'Relief camp "' . $camp->camp_name . '" updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating relief camp', ['error' => $e->getMessage(), 'camp_id' => $id]);
            return redirect()->route('admin.relief-camps')
                ->with('error', 'Error updating relief camp: ' . $e->getMessage());
        }
    }

    public function deleteReliefCamp($id)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Admin access required');
        }

        try {
            DB::beginTransaction();

            $camp = ReliefCamp::findOrFail($id);
            
            // Check if camp has current occupancy
            if ($camp->current_occupancy > 0) {
                return redirect()->route('admin.relief-camps')
                    ->with('error', 'Cannot delete camp "' . $camp->camp_name . '" - it currently has ' . $camp->current_occupancy . ' occupants');
            }

            $campName = $camp->camp_name;
            $camp->delete();

            DB::commit();

            return redirect()->route('admin.relief-camps')
                ->with('success', 'Relief camp "' . $campName . '" has been deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting relief camp', ['error' => $e->getMessage(), 'camp_id' => $id]);
            return redirect()->route('admin.relief-camps')
                ->with('error', 'Error deleting relief camp: ' . $e->getMessage());
        }
    }

    // Chat functionality methods
    public function getChatMessages($volunteerId)
    {
        try {
            $adminId = Auth::user()->user_id;
            
            $messages = ChatMessage::conversation($adminId, $volunteerId)
                ->with(['sender', 'receiver'])
                ->get();

            // Mark messages as read
            ChatMessage::where('sender_id', $volunteerId)
                ->where('receiver_id', $adminId)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'messages' => $messages->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'message' => $message->message,
                        'image_path' => $message->image_path,
                        'sender_type' => $message->sender_type,
                        'sender_name' => $message->sender->first_name . ' ' . $message->sender->last_name,
                        'is_read' => $message->is_read,
                        'created_at' => $message->created_at->format('H:i'),
                        'created_at_full' => $message->created_at->format('Y-m-d H:i:s')
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading messages: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendChatMessage(Request $request)
    {
        $request->validate([
            'volunteer_id' => 'required|string|exists:users,user_id',
            'message' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $adminId = Auth::user()->user_id;
            $imagePath = null;

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('chat_images', $imageName, 'public');
            }

            // Create chat message
            $chatMessage = ChatMessage::create([
                'sender_id' => $adminId,
                'receiver_id' => $request->volunteer_id,
                'message' => $request->message,
                'image_path' => $imagePath,
                'sender_type' => 'admin',
                'is_read' => false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'chat_message' => [
                    'id' => $chatMessage->id,
                    'message' => $chatMessage->message,
                    'image_path' => $chatMessage->image_path,
                    'image_url' => $chatMessage->image_path ? asset('storage/' . $chatMessage->image_path) : null,
                    'sender_type' => $chatMessage->sender_type,
                    'created_at' => $chatMessage->created_at->format('H:i'),
                    'created_at_full' => $chatMessage->created_at->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending message: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getChatVolunteers()
    {
        try {
            $volunteers = User::where('role', 'volunteer')
                ->leftJoin('volunteer_profiles', 'users.user_id', '=', 'volunteer_profiles.volunteer_id')
                ->select(
                    'users.user_id',
                    'users.first_name',
                    'users.last_name',
                    'volunteer_profiles.phone',
                    'volunteer_profiles.is_available'
                )
                ->orderBy('volunteer_profiles.is_available', 'desc')
                ->orderBy('users.first_name')
                ->get();

            return response()->json([
                'success' => true,
                'volunteers' => $volunteers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading volunteers: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignTask(Request $request)
    {
        try {
            // Custom validation rules based on task title type
            $rules = [
                'volunteer_id' => 'required|string|exists:users,user_id',
                'task_title_type' => 'required|in:victim_related,other',
                'task_description' => 'required|string',
                'priority' => 'required|in:high,medium,low',
                'task_type' => 'required|in:relief_distribution,medical_assistance,evacuation_support,communication,logistics,data_collection,other',
                'due_date' => 'required|date|after:now',
                'location' => 'nullable|string|max:255',
                'notes' => 'nullable|string'
            ];

            // Add conditional validation based on task title type
            if ($request->task_title_type === 'victim_related') {
                $rules['victim_id'] = 'required|string|exists:victims,victim_id';
            } else if ($request->task_title_type === 'other') {
                $rules['custom_task_title'] = 'required|string|max:255';
            }

            $request->validate($rules);

            // Determine the task title
            $taskTitle = '';
            $victimId = null;
            
            if ($request->task_title_type === 'victim_related') {
                // Get victim details to create a meaningful title
                $victim = DB::table('victims')->where('victim_id', $request->victim_id)->first();
                if ($victim) {
                    $taskTitle = "Assist {$victim->name} - {$victim->location}";
                    $victimId = $request->victim_id;
                } else {
                    return redirect()->back()->with('error', 'Selected victim not found.');
                }
            } else {
                $taskTitle = $request->custom_task_title;
            }

            // Create the task
            $task = VolunteerTask::create([
                'volunteer_id' => $request->volunteer_id,
                'assigned_by' => Auth::id(),
                'title' => $taskTitle,
                'description' => $request->task_description,
                'priority' => $request->priority,
                'task_type' => $request->task_type,
                'location' => $request->location,
                'due_date' => $request->due_date,
                'notes' => $request->notes,
                'victim_id' => $victimId, // Store victim ID if it's a victim-related task
                'status' => 'pending'
            ]);

            // Get volunteer name for success message
            $volunteer = User::where('user_id', $request->volunteer_id)->first();
            $volunteerName = $volunteer ? $volunteer->first_name . ' ' . $volunteer->last_name : 'Unknown';

            return redirect()->back()->with('success', 
                "Task '{$taskTitle}' has been successfully assigned to {$volunteerName}."
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Please check the form for errors.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error',
                'Error assigning task: ' . $e->getMessage()
            );
        }
    }

    public function inventory(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Admin access required');
        }

        try {
            // Get inventory statistics
            $stats = [
                'total_items' => DB::table('inventory')->sum('quantity') ?? 0,
                'food_items' => DB::table('inventory')->where('item_type', 'food')->sum('quantity') ?? 0,
                'medical_items' => DB::table('inventory')->where('item_type', 'medical')->sum('quantity') ?? 0,
                'clothing_items' => DB::table('inventory')->where('item_type', 'clothing')->sum('quantity') ?? 0,
                'available_items' => DB::table('inventory')->where('status', 'available')->sum('quantity') ?? 0,
            ];

            // Build query with filters
            $query = DB::table('inventory');

            if ($request->filled('search')) {
                $query->where('item_name', 'like', '%' . $request->search . '%');
            }

            if ($request->filled('item_type')) {
                $query->where('item_type', $request->item_type);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $inventory = $query->orderBy('added_date', 'desc')->paginate(15);

            return view('admin.inventory', compact('inventory', 'stats'));

        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')->with('error', 
                'Error loading inventory: ' . $e->getMessage()
            );
        }
    }

    public function storeInventory(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Admin access required');
        }

        try {
            $request->validate([
                'item_name' => 'required|string|max:255',
                'item_type' => 'required|in:food,medical,clothing,other',
                'quantity' => 'required|integer|min:1',
                'item_description' => 'nullable|string',
                'expiry_date' => 'nullable|date|after:today'
            ]);

            $inventoryId = $this->generateUniqueInventoryId();

            DB::table('inventory')->insert([
                'inventory_id' => $inventoryId,
                'item_type' => $request->item_type,
                'quantity' => $request->quantity,
                'item_name' => $request->item_name,
                'item_description' => $request->item_description,
                'added_date' => now(),
                'status' => 'available',
                'expiry_date' => $request->expiry_date,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect()->route('admin.inventory')->with('success', 
                'Inventory item added successfully'
            );

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 
                'Error adding inventory item: ' . $e->getMessage()
            );
        }
    }

    public function updateInventory(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Admin access required');
        }

        try {
            $request->validate([
                'item_name' => 'required|string|max:255',
                'item_type' => 'required|in:food,medical,clothing,other',
                'quantity' => 'required|integer|min:0',
                'item_description' => 'nullable|string',
                'status' => 'required|in:available,reserved,distributed',
                'expiry_date' => 'nullable|date'
            ]);

            $updated = DB::table('inventory')
                ->where('inventory_id', $id)
                ->update([
                    'item_name' => $request->item_name,
                    'item_type' => $request->item_type,
                    'quantity' => $request->quantity,
                    'item_description' => $request->item_description,
                    'status' => $request->status,
                    'expiry_date' => $request->expiry_date,
                    'updated_at' => now()
                ]);

            if ($updated) {
                return redirect()->route('admin.inventory')->with('success', 
                    'Inventory item updated successfully'
                );
            } else {
                return redirect()->back()->with('error', 'Inventory item not found');
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 
                'Error updating inventory item: ' . $e->getMessage()
            );
        }
    }

    public function deleteInventory($id)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Admin access required');
        }

        try {
            $deleted = DB::table('inventory')->where('inventory_id', $id)->delete();

            if ($deleted) {
                return redirect()->route('admin.inventory')->with('success', 
                    'Inventory item deleted successfully'
                );
            } else {
                return redirect()->back()->with('error', 'Inventory item not found');
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 
                'Error deleting inventory item: ' . $e->getMessage()
            );
        }
    }

    public function donations(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Admin access required');
        }

        try {
            // Get donation statistics
            $stats = [
                'total_amount' => Donation::where('status', 'approved')->sum('amount') ?? 0,
                'total_donations' => Donation::count(),
                'monetary_amount' => Donation::whereIn('donation_type', ['monetary', 'money'])
                    ->where('status', 'approved')->sum('amount') ?? 0,
                'monetary_count' => Donation::whereIn('donation_type', ['monetary', 'money'])->count(),
                'physical_count' => Donation::whereNotIn('donation_type', ['monetary', 'money'])->count(),
                'pending_count' => Donation::where('status', 'pending')->count(),
            ];

            // Build query with filters
            $query = Donation::with('user');

            if ($request->filled('search')) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('first_name', 'like', '%' . $request->search . '%')
                      ->orWhere('last_name', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->filled('donation_type')) {
                $query->where('donation_type', $request->donation_type);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('date_from')) {
                $query->where('donation_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('donation_date', '<=', $request->date_to);
            }

            // Handle CSV export
            if ($request->has('export') && $request->export === 'csv') {
                $donations = $query->orderBy('donation_date', 'desc')->get();
                
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="donations_export_' . date('Y-m-d_H-i-s') . '.csv"',
                ];

                $callback = function() use ($donations) {
                    $file = fopen('php://output', 'w');
                    
                    // CSV headers
                    fputcsv($file, [
                        'Donation ID',
                        'Donor Name',
                        'Donor Email',
                        'Donor Phone',
                        'Donation Type',
                        'Amount',
                        'Description',
                        'Quantity',
                        'Status',
                        'Donation Date',
                        'Approved By',
                        'Approved At'
                    ]);

                    // CSV data
                    foreach ($donations as $donation) {
                        fputcsv($file, [
                            $donation->donation_id,
                            $donation->user ? $donation->user->first_name . ' ' . $donation->user->last_name : 'Anonymous',
                            $donation->user ? $donation->user->email : '',
                            $donation->user ? $donation->user->phone : '',
                            ucfirst($donation->donation_type),
                            in_array($donation->donation_type, ['monetary', 'money']) ? $donation->amount : '',
                            $donation->item_description ?? $donation->description ?? '',
                            $donation->quantity ?? '',
                            ucfirst($donation->status),
                            $donation->donation_date ? Carbon::parse($donation->donation_date)->format('Y-m-d H:i:s') : '',
                            $donation->approved_by ?? '',
                            $donation->approved_at ? Carbon::parse($donation->approved_at)->format('Y-m-d H:i:s') : ''
                        ]);
                    }

                    fclose($file);
                };

                return response()->stream($callback, 200, $headers);
            }

            $donations = $query->orderBy('donation_date', 'desc')->paginate(15);

            return view('admin.donations', compact('donations', 'stats'));

        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')->with('error', 
                'Error loading donations: ' . $e->getMessage()
            );
        }
    }

    public function approveDonation($id)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Admin access required');
        }

        try {
            $donation = Donation::where('donation_id', $id)->first();
            
            if (!$donation) {
                return redirect()->back()->with('error', 'Donation not found');
            }

            $donation->status = 'approved';
            $donation->save();

            return redirect()->route('admin.donations')->with('success', 
                'Donation approved successfully'
            );

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 
                'Error approving donation: ' . $e->getMessage()
            );
        }
    }

    public function rejectDonation($id)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Admin access required');
        }

        try {
            $donation = Donation::where('donation_id', $id)->first();
            
            if (!$donation) {
                return redirect()->back()->with('error', 'Donation not found');
            }

            $donation->status = 'rejected';
            $donation->save();

            return redirect()->route('admin.donations')->with('success', 
                'Donation rejected successfully'
            );

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 
                'Error rejecting donation: ' . $e->getMessage()
            );
        }
    }

    public function distributionRepo(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Admin access required');
        }

        try {
            // Get distribution statistics
            $stats = [
                'total_distributions' => DB::table('distribution_records')->count(),
                'completed_distributions' => DB::table('distribution_records')->count(),
                'unique_beneficiaries' => DB::table('distribution_records')
                    ->whereNotNull('victim_id')
                    ->distinct('victim_id')
                    ->count('victim_id'),
                'total_items_distributed' => DB::table('distribution_records')->sum('quantity') ?? 0,
            ];

            // Get volunteers for filter dropdown
            $volunteers = User::where('role', 'volunteer')
                ->select('user_id', 'first_name', 'last_name')
                ->orderBy('first_name')
                ->get();

            // Build query with filters
            $query = DB::table('distribution_records as dr')
                ->leftJoin('victims as v', 'dr.victim_id', '=', 'v.victim_id')
                ->leftJoin('inventory as i', 'dr.inventory_id', '=', 'i.inventory_id')
                ->leftJoin('users as u', 'dr.volunteer_id', '=', 'u.user_id')
                ->select(
                    'dr.*',
                    'v.name as victim_name',
                    'i.item_name',
                    'u.first_name as volunteer_first_name',
                    'u.last_name as volunteer_last_name'
                );

            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('dr.distribution_id', 'like', '%' . $request->search . '%')
                      ->orWhere('v.name', 'like', '%' . $request->search . '%')
                      ->orWhere('dr.location', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->filled('volunteer_id')) {
                $query->where('dr.volunteer_id', $request->volunteer_id);
            }

            if ($request->filled('date_from')) {
                $query->where('dr.distribution_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('dr.distribution_date', '<=', $request->date_to);
            }

            $distributions = $query->orderBy('dr.distribution_date', 'desc')->paginate(15);

            return view('admin.distribution-repo', compact('distributions', 'stats', 'volunteers'));

        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')->with('error', 
                'Error loading distribution repository: ' . $e->getMessage()
            );
        }
    }

    public function exportDistributions(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Admin access required');
        }

        try {
            $query = DB::table('distribution_records as dr')
                ->leftJoin('victims as v', 'dr.victim_id', '=', 'v.victim_id')
                ->leftJoin('inventory as i', 'dr.inventory_id', '=', 'i.inventory_id')
                ->leftJoin('users as u', 'dr.volunteer_id', '=', 'u.user_id')
                ->select(
                    'dr.distribution_id',
                    'dr.distribution_date',
                    'dr.location',
                    'dr.quantity',
                    'v.name as victim_name',
                    'i.item_name',
                    'u.first_name as volunteer_first_name',
                    'u.last_name as volunteer_last_name'
                );

            $distributions = $query->orderBy('dr.distribution_date', 'desc')->get();

            // Create CSV content
            $csvContent = "Distribution ID,Date,Volunteer,Beneficiary,Location,Items,Quantity\n";
            
            foreach ($distributions as $dist) {
                $csvContent .= sprintf(
                    '"%s","%s","%s","%s","%s","%s","%s"' . "\n",
                    $dist->distribution_id,
                    Carbon::parse($dist->distribution_date)->format('Y-m-d H:i'),
                    ($dist->volunteer_first_name ?? '') . ' ' . ($dist->volunteer_last_name ?? ''),
                    $dist->victim_name ?? 'Community Distribution',
                    $dist->location ?? '',
                    $dist->item_name ?? 'N/A',
                    $dist->quantity ?? 0
                );
            }

            $filename = 'distribution_records_' . date('Y-m-d') . '.csv';

            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 
                'Error exporting distributions: ' . $e->getMessage()
            );
        }
    }

    public function storeDistribution(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Admin access required');
        }

        try {
            $request->validate([
                'distribution_location' => 'required|string|max:255',
                'team_leader' => 'required|string|max:255',
                'beneficiaries_count' => 'required|integer|min:1',
                'priority' => 'required|in:high,medium,low',
                'scheduled_date' => 'required|date|after_or_equal:today',
            ]);

            // For now, we'll insert into distribution_records as a simple record
            $distributionId = 'DIST-' . time();
            
            DB::table('distribution_records')->insert([
                'distribution_id' => $distributionId,
                'location' => $request->distribution_location,
                'distribution_date' => $request->scheduled_date,
                'quantity' => $request->beneficiaries_count, // Using quantity field for beneficiaries
                'volunteer_id' => $user->user_id, // Using current admin as volunteer for now
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect()->route('admin.distribution-repo')->with('success', 
                'Distribution scheduled successfully!'
            );

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 
                'Error scheduling distribution: ' . $e->getMessage()
            );
        }
    }
}