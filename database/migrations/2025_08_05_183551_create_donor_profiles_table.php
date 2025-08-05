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
        Schema::create('donor_profiles', function (Blueprint $table) {
            $table->string('donor_id', 20)->primary();
            $table->enum('donor_type', ['individual', 'corporation', 'organization'])->nullable();
            $table->integer('total_donations')->default(0);
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->date('last_donation_date')->nullable();
            $table->timestamps();
            
            $table->foreign('donor_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donor_profiles');
    }
};
