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
            // Get all donations for this user ordered by creation date
            $donations = Donation::where('donor_id', $user->user_id)
                                ->orderBy('donation_date', 'desc')
                                ->get();
            
            // Calculate statistics - ONLY count APPROVED donations
            $approvedDonations = $donations->where('status', 'approved');
            $totalDonations = $approvedDonations->count(); // Only approved donations count
            $totalAmount = $approvedDonations->where('donation_type', 'money')->sum('amount'); // Only approved money donations
            
            // Status breakdown for admin reference (but totals only show approved)
            $approvedCount = $approvedDonations->count();
            $pendingDonations = $donations->where('status', 'pending')->count();
            $rejectedDonations = $donations->where('status', 'rejected')->count();
            
            return view('donor.dashboard', compact(
                'donations', 
                'totalDonations', 
                'totalAmount', 
                'approvedCount', 
                'pendingDonations', 
                'rejectedDonations'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Error in donor dashboard', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Error loading dashboard');
        }
    }

    /**
     * Helper function to display time elapsed (matching PHP version)
     */
    private function timeElapsedString($datetime, $full = false) {
        $now = new \DateTime();
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
    
    public function submitDonation(Request $request)
    {
        $user = Auth::user();
        
        // Basic validation for all donation types
        $validatedData = $request->validate([
            'donation_type' => 'required|in:money,food,clothing,medicine,other',
            'money_amount' => 'nullable|numeric|min:0.01',
            'item_quantity' => 'nullable|string',
            'description' => 'nullable|string',
            'payment_method' => 'nullable|in:bank,mobile,credit,cash',
            'transaction_id' => 'nullable|string',
            'expiry_date' => 'nullable|date',
        ], [
            'money_amount.required' => 'Amount is required for money donations.',
            'money_amount.numeric' => 'Amount must be a valid number.',
            'money_amount.min' => 'Amount must be at least $0.01.',
            'item_quantity.required' => 'Quantity is required for item donations.',
            'donation_type.required' => 'Please select a donation type.',
        ]);

        try {
            // Base donation data
            $donationData = [
                'donation_id' => 'DON-' . time() . rand(100, 999),
                'donor_id' => $user->user_id,
                'donation_type' => $validatedData['donation_type'],
                'donation_date' => now(),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Handle MONEY donations specifically
            if ($validatedData['donation_type'] === 'money') {
                // Validate required fields for money donations
                $request->validate([
                    'money_amount' => 'required|numeric|min:0.01',
                    'payment_method' => 'required|in:bank,mobile,credit,cash',
                    'transaction_id' => 'required|string|min:3',
                ]);

                // Set money-specific fields
                $donationData['amount'] = (float) $validatedData['money_amount'];
                $donationData['payment_method'] = $validatedData['payment_method'];
                $donationData['transaction_id'] = $validatedData['transaction_id'];
                
                // Set other fields to null for money donations
                $donationData['items'] = null;
                $donationData['quantity'] = null;
                $donationData['description'] = null;
                $donationData['expiry_date'] = null;
                
            } else {
                // Handle NON-MONEY donations (food, clothing, medicine, other)
                $request->validate([
                    'item_quantity' => 'required|string|min:1',
                    'description' => 'required|string|min:3',
                ]);

                // Set non-money specific fields
                $donationData['description'] = $validatedData['description'];
                $donationData['items'] = $validatedData['item_quantity']; // Store quantity text as items
                
                // Extract numeric quantity from text
                if (preg_match('/(\d+)/', $validatedData['item_quantity'], $matches)) {
                    $donationData['quantity'] = (int) $matches[1];
                } else {
                    $donationData['quantity'] = 1; // Default quantity
                }
                
                // Add expiry date if provided
                if (!empty($validatedData['expiry_date'])) {
                    $donationData['expiry_date'] = $validatedData['expiry_date'];
                }
                
                // Set money-specific fields to null for non-money donations
                $donationData['amount'] = null;
                $donationData['payment_method'] = null;
                $donationData['transaction_id'] = null;
            }
            
            // Create the donation record
            $donation = Donation::create($donationData);
            
            // Success message based on donation type
            $message = $validatedData['donation_type'] === 'money' 
                ? 'Money donation of $' . number_format($donationData['amount'], 2) . ' submitted successfully!'
                : ucfirst($validatedData['donation_type']) . ' donation submitted successfully!';
            
            return redirect()->route('donor.dashboard')
                           ->with('success', $message . ' It will be reviewed by our team.');
                           
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('donor.dashboard')
                           ->withErrors($e->errors())
                           ->withInput();
        } catch (\Exception $e) {
            \Log::error('Donation submission error: ' . $e->getMessage());
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
        
        // Convert to collection for easier handling in view
        $distributions = collect($distributions);
        
        return view('donor.distribution-repository', compact('distributions'));
    }

    public function editProfile()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in first');
        }
        
        return view('donor.edit-profile', compact('user'));
    }
    
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in first');
        }
        
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        try {
            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                $image = $request->file('profile_picture');
                $imageName = time() . '_' . $user->id . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/profiles'), $imageName);
                $validatedData['profile_picture'] = 'uploads/profiles/' . $imageName;
                
                // Delete old profile picture if exists
                if ($user->profile_picture && file_exists(public_path($user->profile_picture))) {
                    unlink(public_path($user->profile_picture));
                }
            }
            
            // Update user data
            $user->update($validatedData);
            
            return redirect()->route('donor.edit-profile')
                           ->with('success', 'Profile updated successfully!');
                           
        } catch (\Exception $e) {
            return redirect()->route('donor.edit-profile')
                           ->with('error', 'Error updating profile: ' . $e->getMessage());
        }
    }
}
