<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\VolunteerProfile;

class VolunteerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'volunteer') {
            return redirect()->route('login')->with('error', 'Volunteer access required');
        }
        
        try {
            // Get volunteer profile with user information
            $volunteerInfo = DB::table('users')
                ->join('volunteer_profiles', 'users.user_id', '=', 'volunteer_profiles.volunteer_id')
                ->where('users.user_id', $user->user_id)
                ->select('users.*', 'volunteer_profiles.*')
                ->first();
            
            if (!$volunteerInfo) {
                return redirect()->route('login')->with('error', 'Volunteer profile not found');
            }
            
            // Get assigned tasks (using distribution_tasks as task assignments)
            $tasks = DB::table('distribution_tasks as dt')
                ->join('users as v', 'dt.volunteer_id', '=', 'v.user_id')
                ->join('victims as vic', 'dt.victim_id', '=', 'vic.victim_id')
                ->join('inventory as i', 'dt.inventory_id', '=', 'i.inventory_id')
                ->where('dt.volunteer_id', $user->user_id)
                ->where('dt.status', 'pending')
                ->select(
                    'dt.*',
                    'vic.name as victim_name',
                    'vic.priority',
                    'i.item_name as relief_type',
                    'i.item_name'
                )
                ->orderBy('vic.priority', 'desc')
                ->orderBy('dt.assigned_date', 'asc')
                ->get();
            
            // Get completed distributions count
            $completedDistributions = DB::table('distribution_records')
                ->where('volunteer_id', $user->user_id)
                ->count();
            
            return view('volunteer.dashboard', compact('volunteerInfo', 'tasks', 'completedDistributions'));
            
        } catch (\Exception $e) {
            \Log::error('Error in volunteer dashboard', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Error loading dashboard');
        }
    }
    
    public function toggleAvailability(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'volunteer') {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }
        
        try {
            $volunteerProfile = VolunteerProfile::where('volunteer_id', $user->user_id)->first();
            
            if (!$volunteerProfile) {
                return response()->json(['success' => false, 'message' => 'Volunteer profile not found']);
            }
            
            $volunteerProfile->is_available = !$volunteerProfile->is_available;
            $volunteerProfile->save();
            
            return response()->json([
                'success' => true, 
                'is_available' => $volunteerProfile->is_available,
                'status' => $volunteerProfile->is_available ? 'Available' : 'Not Available'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error toggling availability', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error updating availability']);
        }
    }
    
    public function completeTask(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'volunteer') {
            return redirect()->route('volunteer.dashboard')->with('error', 'Unauthorized access');
        }
        
        $request->validate([
            'task_id' => 'required|string'
        ]);
        
        DB::beginTransaction();
        try {
            $taskId = $request->task_id;
            
            // Get task details
            $task = DB::table('distribution_tasks as dt')
                ->join('victims as v', 'dt.victim_id', '=', 'v.victim_id')
                ->join('inventory as i', 'dt.inventory_id', '=', 'i.inventory_id')
                ->where('dt.task_id', $taskId)
                ->where('dt.volunteer_id', $user->user_id)
                ->where('dt.status', 'pending')
                ->select('dt.*', 'v.*', 'i.item_name')
                ->first();
            
            if (!$task) {
                return redirect()->route('volunteer.dashboard')->with('error', 'Task not found or already completed');
            }
            
            // Create distribution record
            $distributionId = 'DIST-' . time();
            DB::table('distribution_records')->insert([
                'distribution_id' => $distributionId,
                'distribution_date' => now(),
                'volunteer_id' => $user->user_id,
                'victim_id' => $task->victim_id,
                'inventory_id' => $task->inventory_id,
                'location' => $task->location,
                'quantity' => $task->quantity,
                'created_at' => now()
            ]);
            
            // Update distribution task status
            DB::table('distribution_tasks')
                ->where('task_id', $taskId)
                ->update([
                    'status' => 'completed',
                    'completion_date' => now()
                ]);
            
            // Update inventory quantity
            DB::table('inventory')
                ->where('inventory_id', $task->inventory_id)
                ->decrement('quantity', $task->quantity);
            
            // Update volunteer profile - set available and increment people helped
            DB::table('volunteer_profiles')
                ->where('volunteer_id', $user->user_id)
                ->update([
                    'is_available' => true,
                    'people_helped' => DB::raw('people_helped + 1')
                ]);
            
            DB::commit();
            return redirect()->route('volunteer.dashboard')->with('success', 'Task completed successfully!');
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error completing task', ['error' => $e->getMessage()]);
            return redirect()->route('volunteer.dashboard')->with('error', 'Error completing task: ' . $e->getMessage());
        }
    }
    
    public function editProfile()
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'volunteer') {
            return redirect()->route('login')->with('error', 'Volunteer access required');
        }
        
        $volunteerProfile = VolunteerProfile::where('volunteer_id', $user->user_id)->first();
        
        return view('volunteer.edit-profile', compact('user', 'volunteerProfile'));
    }
    
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'volunteer') {
            return redirect()->route('login')->with('error', 'Volunteer access required');
        }
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
            'phone' => 'nullable|string|max:20',
            'skill_type' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);
        
        try {
            // Update user information
            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);
            
            // Update volunteer profile
            VolunteerProfile::updateOrCreate(
                ['volunteer_id' => $user->user_id],
                [
                    'skill_type' => $request->skill_type,
                    'location' => $request->location,
                    'emergency_contact_name' => $request->emergency_contact_name,
                    'emergency_contact_phone' => $request->emergency_contact_phone,
                ]
            );
            
            return redirect()->route('volunteer.dashboard')->with('success', 'Profile updated successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Error updating volunteer profile', ['error' => $e->getMessage()]);
            return redirect()->route('volunteer.edit-profile')->with('error', 'Error updating profile: ' . $e->getMessage());
        }
    }
    
    public function distributionRepository()
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'volunteer') {
            return redirect()->route('login')->with('error', 'Volunteer access required');
        }
        
        // Get distribution records for this volunteer
        $distributions = DB::table('distribution_records as dr')
            ->join('victims as v', 'dr.victim_id', '=', 'v.victim_id')
            ->join('inventory as i', 'dr.inventory_id', '=', 'i.inventory_id')
            ->where('dr.volunteer_id', $user->user_id)
            ->select(
                'dr.*',
                'v.name as victim_name',
                'i.item_name as relief_type'
            )
            ->orderBy('dr.distribution_date', 'desc')
            ->paginate(15);
        
        return view('volunteer.distribution-repository', compact('distributions'));
    }
    
    public function inventory(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'volunteer') {
            return redirect()->route('login')->with('error', 'Volunteer access required');
        }
        
        // Get filter parameters
        $filter = $request->get('filter');
        $search = $request->get('search');
        
        // Build query
        $query = DB::table('inventory')->select('*');
        
        // Apply filters
        if ($filter) {
            switch ($filter) {
                case 'low_stock':
                    $query->where('quantity', '<=', 50)->where('quantity', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('quantity', '<=', 0);
                    break;
                case 'good_stock':
                    $query->where('quantity', '>', 50);
                    break;
                case 'food':
                    $query->where('item_type', 'food');
                    break;
                case 'clothing':
                    $query->where('item_type', 'clothing');
                    break;
                case 'medical':
                    $query->where('item_type', 'medical');
                    break;
            }
        }
        
        // Apply search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('item_name', 'like', '%' . $search . '%')
                  ->orWhere('item_description', 'like', '%' . $search . '%');
            });
        }
        
        $inventoryItems = $query->orderBy('item_name')->paginate(15);
        
        return view('volunteer.inventory', compact('inventoryItems'));
    }
    
    public function storeInventory(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'volunteer') {
            return redirect()->route('login')->with('error', 'Volunteer access required');
        }
        
        // Validate the request
        $request->validate([
            'item_name' => 'required|string|max:255',
            'item_type' => 'required|in:food,clothing,medical,other',
            'quantity' => 'required|integer|min:1',
            'item_description' => 'nullable|string',
            'expiry_date' => 'nullable|date|after:today'
        ]);
        
        try {
            // Generate inventory ID
            $lastItem = DB::table('inventory')
                ->whereRaw('inventory_id REGEXP "^[0-9]+$"')
                ->orderByRaw('CAST(inventory_id AS UNSIGNED) DESC')
                ->first();
            
            $nextId = $lastItem ? (intval($lastItem->inventory_id) + 1) : 1;
            
            // Insert new inventory item
            DB::table('inventory')->insert([
                'inventory_id' => (string)$nextId,
                'item_name' => $request->item_name,
                'item_type' => $request->item_type,
                'quantity' => $request->quantity,
                'item_description' => $request->item_description,
                'expiry_date' => $request->expiry_date,
                'status' => 'available',
                'added_date' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return redirect()->route('volunteer.inventory')->with('success', 'Inventory item added successfully!');
            
        } catch (\Exception $e) {
            return redirect()->route('volunteer.inventory')->with('error', 'Failed to add inventory item. Please try again.');
        }
    }
    
    public function deleteInventory($id)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'volunteer') {
            return redirect()->route('login')->with('error', 'Volunteer access required');
        }
        
        try {
            // Check if item exists
            $item = DB::table('inventory')->where('inventory_id', $id)->first();
            
            if (!$item) {
                return redirect()->route('volunteer.inventory')->with('error', 'Inventory item not found.');
            }
            
            // Delete the item
            DB::table('inventory')->where('inventory_id', $id)->delete();
            
            return redirect()->route('volunteer.inventory')->with('success', 'Inventory item deleted successfully!');
            
        } catch (\Exception $e) {
            return redirect()->route('volunteer.inventory')->with('error', 'Failed to delete inventory item. Please try again.');
        }
    }
    
    public function victims()
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'volunteer') {
            return redirect()->route('login')->with('error', 'Volunteer access required');
        }
        
        // Get all victims data
        $victims = DB::table('victims')
            ->select('*')
            ->orderBy('priority', 'desc')
            ->orderBy('name')
            ->paginate(15);
        
        return view('volunteer.victims', compact('victims'));
    }
    
    public function storeVictim(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'volunteer') {
            return redirect()->route('login')->with('error', 'Volunteer access required');
        }
        
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:100',
            'family_size' => 'nullable|integer|min:1',
            'contact_info' => 'nullable|string|max:15',
            'location' => 'required|string|max:100',
            'priority' => 'nullable|in:high,medium,low',
            'special_needs' => 'required|string|max:255'
        ]);
        
        try {
            // Generate victim ID - get the last numeric part and increment
            $lastVictim = DB::table('victims')
                ->whereRaw('victim_id REGEXP "^[0-9]+$"')
                ->orderByRaw('CAST(victim_id AS UNSIGNED) DESC')
                ->first();
            
            $nextId = $lastVictim ? (intval($lastVictim->victim_id) + 1) : 1;
            
            // Insert new victim
            DB::table('victims')->insert([
                'victim_id' => (string)$nextId,
                'name' => $request->name,
                'family_size' => $request->family_size ?? 1,
                'phone' => $request->contact_info,
                'location' => $request->location,
                'priority' => $request->priority ?? 'low',
                'needs' => $request->special_needs,
                'status' => 'pending',
                'registration_date' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return redirect()->route('volunteer.victims')->with('success', 'Victim record added successfully!');
            
        } catch (\Exception $e) {
            return redirect()->route('volunteer.victims')->with('error', 'Failed to add victim record. Please try again.');
        }
    }
}
