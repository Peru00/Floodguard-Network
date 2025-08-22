<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;
use App\Models\User;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration by sending a sample password reset email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('Testing email configuration...');
        
        try {
            // Create a dummy user for testing
            $user = (object) [
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => $email
            ];
            
            $resetLink = 'https://example.com/test-reset-link';
            
            Mail::to($email)->send(new PasswordResetMail($resetLink, $user));
            
            $this->info('✅ Email sent successfully to: ' . $email);
            $this->info('📧 Check your inbox and spam folder.');
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
            $this->info('💡 Make sure your email configuration in .env is correct.');
        }
    }
}
