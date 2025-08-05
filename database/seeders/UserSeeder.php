<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\VolunteerProfile;
use App\Models\DonorProfile;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'user_id' => 'USER-ADMIN-001',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@floodguard.com',
            'password' => Hash::make('password'),
            'phone' => '01234567890',
            'role' => 'admin',
            'registration_date' => now(),
            'status' => 'active'
        ]);

        // Create Volunteer User
        $volunteer = User::create([
            'user_id' => 'USER-VOL-001',
            'first_name' => 'John',
            'last_name' => 'Volunteer',
            'email' => 'volunteer@floodguard.com',
            'password' => Hash::make('password'),
            'phone' => '01234567891',
            'role' => 'volunteer',
            'registration_date' => now(),
            'status' => 'active'
        ]);

        // Create volunteer profile
        VolunteerProfile::create([
            'volunteer_id' => $volunteer->user_id,
            'location' => 'Dhaka',
            'skill_type' => 'Logistics',
            'is_available' => true,
            'people_helped' => 0
        ]);

        // Create Donor User
        $donor = User::create([
            'user_id' => 'USER-DON-001',
            'first_name' => 'Jane',
            'last_name' => 'Donor',
            'email' => 'donor@floodguard.com',
            'password' => Hash::make('password'),
            'phone' => '01234567892',
            'role' => 'donor',
            'registration_date' => now(),
            'status' => 'active'
        ]);

        // Create donor profile
        DonorProfile::create([
            'donor_id' => $donor->user_id,
            'donor_type' => 'individual',
            'total_donations' => 0,
            'total_amount' => 0.00
        ]);
    }
}
