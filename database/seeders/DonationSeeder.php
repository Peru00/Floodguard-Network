<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Donation;

class DonationSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create some sample donations for testing
        $donations = [
            [
                'donation_id' => 'DON-' . time() . '-001',
                'donor_id' => 'USER-DON-001', // Jane Donor from UserSeeder
                'donation_type' => 'monetary',
                'amount' => 500.00,
                'items' => null,
                'quantity' => null,
                'payment_method' => 'credit_card',
                'transaction_id' => 'TXN-' . time() . '-001',
                'donation_date' => now()->subDays(1),
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
            ],
            [
                'donation_id' => 'DON-' . time() . '-002',
                'donor_id' => 'USER-DON-001',
                'donation_type' => 'food',
                'amount' => null,
                'items' => 'Rice, Oil, Lentils',
                'quantity' => 50,
                'payment_method' => null,
                'transaction_id' => null,
                'donation_date' => now()->subDays(2),
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
            ],
            [
                'donation_id' => 'DON-' . time() . '-003',
                'donor_id' => 'USER-DON-001',
                'donation_type' => 'clothing',
                'amount' => null,
                'items' => 'Winter Clothes, Blankets',
                'quantity' => 20,
                'payment_method' => null,
                'transaction_id' => null,
                'donation_date' => now()->subDays(3),
                'status' => 'approved',
                'approved_by' => 'USER-ADMIN-001',
                'approved_at' => now()->subDays(2),
            ],
            [
                'donation_id' => 'DON-' . time() . '-004',
                'donor_id' => 'USER-DON-001',
                'donation_type' => 'monetary',
                'amount' => 1000.00,
                'items' => null,
                'quantity' => null,
                'payment_method' => 'bank_transfer',
                'transaction_id' => 'TXN-' . time() . '-004',
                'donation_date' => now()->subDays(4),
                'status' => 'approved',
                'approved_by' => 'USER-ADMIN-001',
                'approved_at' => now()->subDays(3),
            ],
            [
                'donation_id' => 'DON-' . time() . '-005',
                'donor_id' => 'USER-DON-001',
                'donation_type' => 'medical',
                'amount' => null,
                'items' => 'First Aid Kits, Bandages',
                'quantity' => 10,
                'payment_method' => null,
                'transaction_id' => null,
                'donation_date' => now()->subDays(5),
                'status' => 'rejected',
                'approved_by' => 'USER-ADMIN-001',
                'approved_at' => now()->subDays(4),
            ],
        ];

        foreach ($donations as $donation) {
            Donation::create($donation);
        }
    }
}
