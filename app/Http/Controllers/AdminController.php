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
            // Get dashboard statistics
            $stats = [
                'volunteers' => User::where('role', 'volunteer')->count(),
                'donors' => User::where('role', 'donor')->count(),
                'total_donations' => Donation::where('status', 'approved')->sum('amount'),
                'distributions' => DB::table('distribution_records')->sum('quantity') ?? 0,
                'locations' => DB::table('victims')->distinct('location')->count('location') ?? 0
            ];

            // Get recent donations with donor information
            $recentDonations = Donation::with('user')
                ->orderBy('donation_date', 'desc')
                ->limit(5)
                ->get();

            // Get pending donations for approval
            $pendingDonations = Donation::with('user')
                ->where('status', 'pending')
                ->orderBy('donation_date', 'desc')
                ->get();

            return view('admin.dashboard', compact('stats', 'recentDonations', 'pendingDonations'));
            
        } catch (\Exception $e) {
            \Log::error('Error in admin dashboard', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Error loading dashboard');
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
