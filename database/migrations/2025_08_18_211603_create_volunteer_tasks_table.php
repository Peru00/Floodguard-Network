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
        Schema::create('volunteer_tasks', function (Blueprint $table) {
            $table->string('task_id', 50)->primary();
            $table->string('volunteer_id', 50);
            $table->string('assigned_by', 50);
            $table->string('title', 255);
            $table->text('description');
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->enum('task_type', [
                'relief_distribution', 
                'medical_assistance', 
                'evacuation_support', 
                'communication', 
                'logistics', 
                'data_collection', 
                'other'
            ])->default('other');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->string('location', 255)->nullable();
            $table->datetime('due_date');
            $table->datetime('assigned_date')->default(now());
            $table->datetime('completed_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Add foreign key constraints
            $table->foreign('volunteer_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_tasks');
    }
};
