<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\VolunteerProfile;
use App\Models\DonorProfile;

class CreateTestUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:test-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test user for login testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = 'USER-TEST-' . time();
        
        // Create test user
        $user = User::create([
            'user_id' => $userId,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@test.com',
            'password' => Hash::make('123456'),
            'phone' => '01234567890',
            'role' => 'admin',
            'registration_date' => now(),
            'status' => 'active'
        ]);

        $this->info('Test user created successfully!');
        $this->info('Email: test@test.com');
        $this->info('Password: 123456');
        $this->info('Role: admin');
        $this->info('User ID: ' . $userId);
        
        return 0;
    }
}
