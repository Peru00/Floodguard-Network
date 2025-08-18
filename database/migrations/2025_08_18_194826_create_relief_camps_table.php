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
        Schema::create('relief_camps', function (Blueprint $table) {
            $table->string('camp_id', 20)->primary();
            $table->string('camp_name', 100);
            $table->string('location', 255);
            $table->integer('capacity');
            $table->integer('current_occupancy')->default(0);
            $table->string('managed_by', 20)->nullable();
            $table->timestamp('last_updated')->useCurrent()->useCurrentOnUpdate();
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('managed_by')->references('user_id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index('location');
            $table->index('managed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relief_camps');
    }
};
