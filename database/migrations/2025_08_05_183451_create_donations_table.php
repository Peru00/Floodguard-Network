<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->string('donation_id', 20)->primary();
            $table->string('donor_id', 20);
            $table->enum('donation_type', ['monetary', 'food', 'clothing', 'medical', 'other']);
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('items', 255)->nullable();
            $table->integer('quantity')->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('transaction_id', 100)->nullable();
            $table->dateTime('donation_date');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('approved_by', 20)->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->date('expiry_date')->nullable()->comment('Expiry date for perishable donations (food, medicine)');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('donor_id')->references('user_id')->on('users');
            $table->foreign('approved_by')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
