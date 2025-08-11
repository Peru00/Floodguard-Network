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
        // Drop the empty distribution_tasks table
        Schema::dropIfExists('distribution_tasks');
        
        // Create the proper distribution_records table
        Schema::create('distribution_records', function (Blueprint $table) {
            $table->string('distribution_id', 50)->primary();
            $table->datetime('distribution_date')->nullable();
            $table->string('volunteer_id', 50)->nullable();
            $table->string('victim_id', 50)->nullable();
            $table->string('inventory_id', 50)->nullable();
            $table->string('location', 255)->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            // Add foreign key constraints
            $table->foreign('volunteer_id')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('victim_id')->references('victim_id')->on('victims')->onDelete('set null');
            $table->foreign('inventory_id')->references('inventory_id')->on('inventory')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribution_records');
        
        // Recreate the original distribution_tasks table
        Schema::create('distribution_tasks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
};
