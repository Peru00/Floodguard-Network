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
        Schema::table('volunteer_tasks', function (Blueprint $table) {
            $table->string('victim_id')->nullable()->after('volunteer_id');
            $table->foreign('victim_id')->references('victim_id')->on('victims')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('volunteer_tasks', function (Blueprint $table) {
            $table->dropForeign(['victim_id']);
            $table->dropColumn('victim_id');
        });
    }
};
