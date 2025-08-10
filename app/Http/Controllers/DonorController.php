<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\DonorProfile;
use App\Models\Donation;

class DonorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in first');
        }
        
        try {
            // Get donor profile data
            $donorInfo = (object)[
                'user_id' => $user->user_id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'total_donations' => 0,
                'total_amount' => 0,
                'last_donation_date' => null
            ];
            
            // Get recent donations (simplified for now)
            $recentDonations = collect([]);
            
            return view('donor.dashboard', compact('donorInfo', 'recentDonations'));
            
        } catch (\Exception $e) {
            \Log::error('Error in donor dashboard', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Error loading dashboard');
        }
    }
    
    public function submitDonation(Request $request)
    {
        $user = Auth::user();
        
        $validatedData = $request->validate([
            'donation_type' => 'required|in:monetary,goods,services',
            'amount' => 'nullable|numeric|min:0',
            'items' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:1',
            'payment_method' => 'nullable|string|max:50',
            'transaction_id' => 'nullable|string|max:100',
        ]);
        
        try {
            $donationData = [
                'donation_id' => 'DON-' . time() . rand(100, 999),
                'donor_id' => $user->user_id,
                'donation_type' => $validatedData['donation_type'],
                'donation_date' => now(),
                'status' => 'pending'
            ];
            
            if ($validatedData['donation_type'] === 'monetary') {
                $donationData['amount'] = $validatedData['amount'];
                $donationData['payment_method'] = $validatedData['payment_method'];
                $donationData['transaction_id'] = $validatedData['transaction_id'];
            } else {
                $donationData['items'] = $validatedData['items'];
                $donationData['quantity'] = $validatedData['quantity'];
            }
            
            DB::table('donations')->insert($donationData);
            
            return redirect()->route('donor.dashboard')
                           ->with('success', 'Donation submitted successfully! It will be reviewed by our team.');
        } catch (\Exception $e) {
            return redirect()->route('donor.dashboard')
                           ->with('error', 'Error submitting donation: ' . $e->getMessage());
        }
    }
    
    public function donations()
    {
        $user = Auth::user();
        
        $donations = DB::table('donations')
            ->where('donor_id', $user->user_id)
            ->orderBy('donation_date', 'desc')
            ->get();
        
        return view('donor.donations', compact('donations'));
    }
    
    public function viewDonation($donationId)
    {
        $user = Auth::user();
        
        $donation = DB::table('donations')
            ->join('users', 'donations.donor_id', '=', 'users.user_id')
            ->where('donations.donation_id', $donationId)
            ->where('donations.donor_id', $user->user_id)
            ->select('donations.*', 'users.first_name', 'users.last_name')
            ->first();
        
        if (!$donation) {
            return redirect()->route('donor.donations')
                           ->with('error', 'Donation not found.');
        }
        
        return view('donor.view-donation', compact('donation'));
    }
    
    public function distributionRepository()
    {
        // Get distribution records to show donors how their contributions are being used
        $distributions = DB::select("
            SELECT 
                dr.distribution_id,
                dr.distribution_date,
                CONCAT(v.first_name, ' ', v.last_name) as volunteer_name,
                vic.name as victim_name,
                i.item_name as relief_type,
                dr.location,
                dr.quantity
            FROM distribution_records dr
            JOIN users v ON dr.volunteer_id = v.user_id
            JOIN victims vic ON dr.victim_id = vic.victim_id
            JOIN inventory i ON dr.inventory_id = i.inventory_id
            ORDER BY dr.distribution_date DESC
            LIMIT 50
        ");
        
        return view('donor.distribution-repository', compact('distributions'));
    }
}
