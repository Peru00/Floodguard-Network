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
        Schema::create('victims', function (Blueprint $table) {
            $table->string('victim_id', 20)->primary();
            $table->string('name', 100);
            $table->integer('family_size');
            $table->string('phone', 15)->nullable();
            $table->string('location', 100);
            $table->enum('priority', ['high', 'medium', 'low']);
            $table->string('needs');
            $table->enum('status', ['pending', 'assisted', 'relocated'])->default('pending');
            $table->datetime('registration_date')->default(now());
            $table->string('assisted_by', 20)->nullable();
            $table->datetime('assisted_date')->nullable();
            $table->timestamps();
            
            $table->foreign('assisted_by')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('victims');
    }
};
