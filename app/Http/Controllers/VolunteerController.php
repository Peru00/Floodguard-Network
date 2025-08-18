<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\VolunteerProfile;
use App\Models\ChatMessage;

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

    /**
     * Show the relief camps page for volunteer
     */
    public function reliefCamps()
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'volunteer') {
            return redirect()->route('login')->with('error', 'Volunteer access required');
        }

        try {
            // Get volunteer profile information
            $volunteerInfo = DB::table('users')
                ->join('volunteer_profiles', 'users.user_id', '=', 'volunteer_profiles.volunteer_id')
                ->where('users.user_id', $user->user_id)
                ->select('users.*', 'volunteer_profiles.*')
                ->first();

            if (!$volunteerInfo) {
                return redirect()->route('login')->with('error', 'Volunteer profile not found');
            }

            // Get the camp that this volunteer is assigned to manage
            $assignedCamp = DB::table('relief_camps')
                ->where('managed_by', $user->user_id)
                ->first();

            // If camp exists, calculate occupancy details
            if ($assignedCamp) {
                // Convert to array for easier manipulation
                $assignedCamp = (array) $assignedCamp;
                
                $assignedCamp['available_spaces'] = $assignedCamp['capacity'] - $assignedCamp['current_occupancy'];
                $assignedCamp['occupancy_percentage'] = $assignedCamp['capacity'] > 0 
                    ? ($assignedCamp['current_occupancy'] / $assignedCamp['capacity']) * 100 
                    : 0;
                
                // Determine occupancy status
                if ($assignedCamp['occupancy_percentage'] >= 100) {
                    $assignedCamp['occupancy_status'] = 'full';
                } elseif ($assignedCamp['occupancy_percentage'] >= 80) {
                    $assignedCamp['occupancy_status'] = 'almost-full';
                } else {
                    $assignedCamp['occupancy_status'] = 'available';
                }

                // Convert timestamps to Carbon objects for proper formatting
                $assignedCamp['created_at'] = \Carbon\Carbon::parse($assignedCamp['created_at']);
                $assignedCamp['updated_at'] = \Carbon\Carbon::parse($assignedCamp['updated_at']);

                // Convert back to object for easier access in view
                $assignedCamp = (object) $assignedCamp;
            }

            return view('volunteer.relief-camps', compact('volunteerInfo', 'assignedCamp'));

        } catch (\Exception $e) {
            return redirect()->route('volunteer.dashboard')->with('error', 'Failed to load relief camps page. Please try again.');
        }
    }

    /**
     * Update camp occupancy
     */
    public function updateCampOccupancy(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'volunteer') {
            return redirect()->route('login')->with('error', 'Volunteer access required');
        }

        $request->validate([
            'camp_id' => 'required|string',
            'current_occupancy' => 'required|integer|min:0',
            'update_notes' => 'nullable|string|max:255'
        ]);

        try {
            // Verify that this volunteer manages this camp
            $camp = DB::table('relief_camps')
                ->where('camp_id', $request->camp_id)
                ->where('managed_by', $user->user_id)
                ->first();

            if (!$camp) {
                return redirect()->route('volunteer.relief-camps')->with('error', 'You are not authorized to manage this camp.');
            }

            // Validate occupancy doesn't exceed capacity
            if ($request->current_occupancy > $camp->capacity) {
                return redirect()->route('volunteer.relief-camps')->with('error', 'Occupancy cannot exceed camp capacity of ' . $camp->capacity . ' people.');
            }

            // Update the camp occupancy
            DB::table('relief_camps')
                ->where('camp_id', $request->camp_id)
                ->update([
                    'current_occupancy' => $request->current_occupancy,
                    'updated_at' => now()
                ]);

            $message = 'Camp occupancy updated successfully.';
            if ($request->update_notes) {
                $message .= ' Notes: ' . $request->update_notes;
            }

            return redirect()->route('volunteer.relief-camps')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('volunteer.relief-camps')->with('error', 'Failed to update camp occupancy. Please try again.');
        }
    }

    /**
     * Generate camp report
     */
    public function generateCampReport(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'volunteer') {
            return response()->json(['success' => false, 'message' => 'Volunteer access required'], 403);
        }

        $request->validate([
            'camp_id' => 'required|string'
        ]);

        try {
            // Verify that this volunteer manages this camp
            $camp = DB::table('relief_camps')
                ->where('camp_id', $request->camp_id)
                ->where('managed_by', $user->user_id)
                ->first();

            if (!$camp) {
                return response()->json(['success' => false, 'message' => 'You are not authorized to generate reports for this camp.'], 403);
            }

            // Get volunteer information
            $volunteerInfo = DB::table('users')
                ->join('volunteer_profiles', 'users.user_id', '=', 'volunteer_profiles.volunteer_id')
                ->where('users.user_id', $user->user_id)
                ->select('users.*', 'volunteer_profiles.*')
                ->first();

            // Get additional statistics
            $reportData = [
                'camp' => $camp,
                'volunteer' => $volunteerInfo,
                'generated_at' => now(),
                'occupancy_percentage' => $camp->capacity > 0 ? ($camp->current_occupancy / $camp->capacity) * 100 : 0,
                'available_spaces' => $camp->capacity - $camp->current_occupancy,
                'occupancy_status' => $this->getOccupancyStatus($camp->capacity, $camp->current_occupancy)
            ];

            // Generate HTML report content
            $htmlContent = $this->generateReportHTML($reportData);

            // For now, return HTML content as JSON for demo purposes
            // In production, you would use a PDF generation library like DomPDF or wkhtmltopdf
            return response()->json([
                'success' => true, 
                'message' => 'Report generated successfully!',
                'report_html' => $htmlContent,
                'download_url' => route('volunteer.download-camp-report', ['camp_id' => $request->camp_id])
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to generate report. Please try again.'], 500);
        }
    }

    /**
     * Helper method to determine occupancy status
     */
    private function getOccupancyStatus($capacity, $currentOccupancy)
    {
        if ($capacity <= 0) return 'unknown';
        
        $percentage = ($currentOccupancy / $capacity) * 100;
        
        if ($percentage >= 100) return 'full';
        if ($percentage >= 80) return 'almost-full';
        return 'available';
    }

    /**
     * Generate HTML content for the report
     */
    private function generateReportHTML($data)
    {
        $camp = $data['camp'];
        $volunteer = $data['volunteer'];
        $generatedAt = $data['generated_at'];
        $occupancyPercentage = number_format($data['occupancy_percentage'], 1);
        $availableSpaces = $data['available_spaces'];
        $occupancyStatus = ucfirst(str_replace('-', ' ', $data['occupancy_status']));

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Relief Camp Report - {$camp->camp_name}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
                .section { margin-bottom: 25px; }
                .section h3 { color: #333; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
                .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
                .info-item { padding: 10px; background: #f5f5f5; border-radius: 5px; }
                .status-{$data['occupancy_status']} { 
                    padding: 5px 10px; 
                    border-radius: 15px; 
                    display: inline-block;
                    font-weight: bold;
                }
                .status-available { background: #d1fae5; color: #059669; }
                .status-almost-full { background: #fef3c7; color: #d97706; }
                .status-full { background: #fee2e2; color: #dc2626; }
                .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>Relief Camp Management Report</h1>
                <h2>{$camp->camp_name}</h2>
                <p>Generated on: {$generatedAt->format('F j, Y g:i A')}</p>
            </div>
            
            <div class='section'>
                <h3>Camp Information</h3>
                <div class='info-grid'>
                    <div class='info-item'>
                        <strong>Camp Name:</strong> {$camp->camp_name}
                    </div>
                    <div class='info-item'>
                        <strong>Location:</strong> {$camp->location}
                    </div>
                    <div class='info-item'>
                        <strong>Total Capacity:</strong> {$camp->capacity} people
                    </div>
                    <div class='info-item'>
                        <strong>Current Occupancy:</strong> {$camp->current_occupancy} people
                    </div>
                    <div class='info-item'>
                        <strong>Available Spaces:</strong> {$availableSpaces} spaces
                    </div>
                    <div class='info-item'>
                        <strong>Occupancy Rate:</strong> {$occupancyPercentage}%
                    </div>
                    <div class='info-item'>
                        <strong>Status:</strong> <span class='status-{$data['occupancy_status']}'>{$occupancyStatus}</span>
                    </div>
                    <div class='info-item'>
                        <strong>Last Updated:</strong> " . \Carbon\Carbon::parse($camp->updated_at)->format('M d, Y g:i A') . "
                    </div>
                </div>
            </div>
            
            <div class='section'>
                <h3>Manager Information</h3>
                <div class='info-grid'>
                    <div class='info-item'>
                        <strong>Manager Name:</strong> {$volunteer->first_name} {$volunteer->last_name}
                    </div>
                    <div class='info-item'>
                        <strong>Volunteer ID:</strong> {$volunteer->volunteer_id}
                    </div>
                    <div class='info-item'>
                        <strong>Contact Phone:</strong> " . ($volunteer->phone ?? 'Not provided') . "
                    </div>
                    <div class='info-item'>
                        <strong>Email:</strong> {$volunteer->email}
                    </div>
                </div>
            </div>
            
            <div class='section'>
                <h3>Summary</h3>
                <p>This report provides an overview of the {$camp->camp_name} relief camp as of {$generatedAt->format('F j, Y')}. 
                The camp is currently operating at {$occupancyPercentage}% capacity with {$availableSpaces} spaces available for new occupants.</p>
            </div>
            
            <div class='footer'>
                <p>Generated by FloodGuard Network - Relief Camp Management System</p>
                <p>Report generated by: {$volunteer->first_name} {$volunteer->last_name} on {$generatedAt->format('F j, Y g:i A')}</p>
            </div>
        </body>
        </html>";
    }

    /**
     * Download camp report as HTML file
     */
    public function downloadCampReport($campId)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'volunteer') {
            return redirect()->route('login')->with('error', 'Volunteer access required');
        }

        try {
            // Verify that this volunteer manages this camp
            $camp = DB::table('relief_camps')
                ->where('camp_id', $campId)
                ->where('managed_by', $user->user_id)
                ->first();

            if (!$camp) {
                return redirect()->route('volunteer.relief-camps')->with('error', 'You are not authorized to download reports for this camp.');
            }

            // Get volunteer information
            $volunteerInfo = DB::table('users')
                ->join('volunteer_profiles', 'users.user_id', '=', 'volunteer_profiles.volunteer_id')
                ->where('users.user_id', $user->user_id)
                ->select('users.*', 'volunteer_profiles.*')
                ->first();

            // Prepare report data
            $reportData = [
                'camp' => $camp,
                'volunteer' => $volunteerInfo,
                'generated_at' => now(),
                'occupancy_percentage' => $camp->capacity > 0 ? ($camp->current_occupancy / $camp->capacity) * 100 : 0,
                'available_spaces' => $camp->capacity - $camp->current_occupancy,
                'occupancy_status' => $this->getOccupancyStatus($camp->capacity, $camp->current_occupancy)
            ];

            // Generate HTML content
            $htmlContent = $this->generateReportHTML($reportData);

            // Return as downloadable HTML file
            $filename = $camp->camp_name . '_Report_' . now()->format('Y-m-d_H-i-s') . '.html';
            
            return response($htmlContent)
                ->header('Content-Type', 'text/html')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            return redirect()->route('volunteer.relief-camps')->with('error', 'Failed to download report. Please try again.');
        }
    }

    // Chat functionality methods
    public function getChatMessages()
    {
        try {
            $volunteerId = Auth::user()->user_id;
            
            // Get all messages between this volunteer and admins
            $messages = ChatMessage::where(function($query) use ($volunteerId) {
                $query->where('sender_id', $volunteerId)
                      ->orWhere('receiver_id', $volunteerId);
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

            // Mark admin messages as read
            ChatMessage::where('receiver_id', $volunteerId)
                ->where('sender_type', 'admin')
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
            'message' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $volunteerId = Auth::user()->user_id;
            $imagePath = null;

            // Get admin user (assuming there's at least one admin)
            $admin = User::where('role', 'admin')->first();
            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'No admin available to receive messages'
                ], 404);
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('chat_images', $imageName, 'public');
            }

            // Create chat message
            $chatMessage = ChatMessage::create([
                'sender_id' => $volunteerId,
                'receiver_id' => $admin->user_id,
                'message' => $request->message,
                'image_path' => $imagePath,
                'sender_type' => 'volunteer',
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
}
