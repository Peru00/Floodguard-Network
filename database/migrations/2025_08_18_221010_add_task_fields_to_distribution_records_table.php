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
        Schema::table('distribution_records', function (Blueprint $table) {
            $table->string('task_type')->nullable()->after('quantity');
            $table->string('task_title')->nullable()->after('task_type');
            $table->text('task_description')->nullable()->after('task_title');
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distribution_records', function (Blueprint $table) {
            $table->dropColumn(['task_type', 'task_title', 'task_description', 'updated_at']);
        });
    }
};
