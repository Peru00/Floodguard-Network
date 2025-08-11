<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class VolunteerSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        // Check if volunteer user already exists
        $existingVolunteer = DB::table('users')->where('email', 'volunteer@floodguard.com')->first();
        
        if (!$existingVolunteer) {
            // Create a sample volunteer user
            $volunteerId = 'VOL-' . time();
            
            // Insert into users table
            DB::table('users')->insert([
                'user_id' => $volunteerId,
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'volunteer@floodguard.com',
                'phone' => '+1 (555) 123-4567',
                'password' => Hash::make('password'),
                'role' => 'volunteer',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Insert into volunteer_profiles table
            DB::table('volunteer_profiles')->insert([
                'volunteer_id' => $volunteerId,
                'skill_type' => 'Medical Assistance',
                'location' => 'Downtown District',
                'is_available' => true,
                'people_helped' => 15,
                'emergency_contact_name' => 'Jane Smith',
                'emergency_contact_phone' => '+1 (555) 987-6543',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $volunteerId = $existingVolunteer->user_id;
            
            // Update existing volunteer profile if it exists
            DB::table('volunteer_profiles')->updateOrInsert(
                ['volunteer_id' => $volunteerId],
                [
                    'skill_type' => 'Medical Assistance',
                    'location' => 'Downtown District',
                    'is_available' => true,
                    'people_helped' => 15,
                    'emergency_contact_name' => 'Jane Smith',
                    'emergency_contact_phone' => '+1 (555) 987-6543',
                    'updated_at' => now(),
                ]
            );
        }
        
        // Create sample victims if they don't exist
        $victims = [
            [
                'victim_id' => 'VIC-001',
                'name' => 'Maria Rodriguez',
                'family_size' => 4,
                'phone' => '+1 (555) 111-2222',
                'location' => 'Riverside Area',
                'needs' => 'Food, Medical supplies',
                'priority' => 'high',
                'status' => 'pending',
                'registration_date' => now(),
                'created_at' => now()
            ],
            [
                'victim_id' => 'VIC-002', 
                'name' => 'Ahmed Hassan',
                'family_size' => 2,
                'phone' => '+1 (555) 333-4444',
                'location' => 'North District',
                'needs' => 'Clothing, Shelter',
                'priority' => 'medium',
                'status' => 'pending',
                'registration_date' => now(),
                'created_at' => now()
            ]
        ];
        
        foreach ($victims as $victim) {
            DB::table('victims')->insertOrIgnore($victim);
        }
        
        // Create sample inventory if it doesn't exist
        $inventory = [
            [
                'inventory_id' => 'INV-001',
                'item_name' => 'Food Packages',
                'item_type' => 'food',
                'quantity' => 100,
                'item_description' => 'Emergency food packages with canned goods and essentials',
                'added_date' => now(),
                'status' => 'available',
                'created_at' => now()
            ],
            [
                'inventory_id' => 'INV-002',
                'item_name' => 'Medical Supplies',
                'item_type' => 'medical',
                'quantity' => 50,
                'item_description' => 'Basic medical kits with first aid supplies',
                'added_date' => now(),
                'status' => 'available',
                'created_at' => now()
            ]
        ];
        
        foreach ($inventory as $item) {
            DB::table('inventory')->insertOrIgnore($item);
        }
        
        // Create sample distribution tasks - simplified for now
        // Note: In a real system, tasks would be created by admin and converted to distribution_records when completed
        $this->command->info('Created sample victims and inventory.');
        $this->command->info('Tasks would normally be assigned by administrators.');
        
        $this->command->info('Volunteer sample data created successfully!');
        $this->command->info('Volunteer login: volunteer@floodguard.com / password');
    }
}
