<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        // Get the volunteer user_id
        $volunteer = DB::table('users')->where('email', 'volunteer@floodguard.com')->first();
        
        if (!$volunteer) {
            $this->command->error('Volunteer user not found. Please run VolunteerSeeder first.');
            return;
        }
        
        // Create sample distribution tasks
        $tasks = [
            [
                'task_id' => 'TASK-' . time() . '-001',
                'volunteer_id' => $volunteer->user_id,
                'inventory_id' => 'INV-001',
                'victim_id' => 'VIC-001',
                'quantity' => 5,
                'location' => 'Riverside Area',
                'status' => 'pending',
                'assigned_date' => now(),
                'notes' => 'High priority - family with young children needs immediate assistance',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'task_id' => 'TASK-' . time() . '-002',
                'volunteer_id' => $volunteer->user_id,
                'inventory_id' => 'INV-002',
                'victim_id' => 'VIC-002',
                'quantity' => 2,
                'location' => 'North District',
                'status' => 'pending',
                'assigned_date' => now()->addHour(),
                'notes' => 'Medical supplies needed for elderly residents',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];
        
        foreach ($tasks as $task) {
            DB::table('distribution_tasks')->insertOrIgnore($task);
        }
        
        $this->command->info('Distribution tasks created successfully!');
    }
}
