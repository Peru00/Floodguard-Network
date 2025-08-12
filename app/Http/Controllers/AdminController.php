<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            // Get basic statistics
            $stats = [
                'volunteers' => User::where('role', 'volunteer')->count(),
                'donors' => User::where('role', 'donor')->count(),
                'total_donations' => Donation::where('status', 'approved')->sum('amount') ?? 0,
                'distributions' => 0, // Simplified for now
                'locations' => 0 // Simplified for now
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
                ->select('victim_id', 'name', 'location', 'relief_needed', 'status', 'created_at')
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
}
