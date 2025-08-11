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
    
    public function inventory()
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'volunteer') {
            return redirect()->route('login')->with('error', 'Volunteer access required');
        }
        
        // Get all inventory items
        $inventoryItems = DB::table('inventory')
            ->select('*')
            ->orderBy('item_name')
            ->paginate(15);
        
        return view('volunteer.inventory', compact('inventoryItems'));
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
}
