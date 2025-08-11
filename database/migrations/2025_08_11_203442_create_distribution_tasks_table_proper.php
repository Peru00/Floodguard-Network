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
        Schema::create('distribution_tasks', function (Blueprint $table) {
            $table->string('task_id', 50)->primary();
            $table->string('volunteer_id', 50)->nullable();
            $table->string('inventory_id', 50)->nullable();
            $table->string('victim_id', 50)->nullable();
            $table->integer('quantity')->nullable();
            $table->string('location', 255)->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->datetime('assigned_date')->default(now());
            $table->datetime('completion_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
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
        Schema::dropIfExists('distribution_tasks');
    }
};
