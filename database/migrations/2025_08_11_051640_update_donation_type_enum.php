<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, temporarily expand the enum to include both old and new values
        DB::statement("ALTER TABLE donations MODIFY COLUMN donation_type ENUM('monetary','money','food','clothing','medical','medicine','other') NOT NULL");
        
        // Then update existing data to new values
        DB::statement("UPDATE donations SET donation_type = 'money' WHERE donation_type = 'monetary'");
        DB::statement("UPDATE donations SET donation_type = 'medicine' WHERE donation_type = 'medical'");
        
        // Finally, set the enum to only new values
        DB::statement("ALTER TABLE donations MODIFY COLUMN donation_type ENUM('money','food','clothing','medicine','other') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, expand enum to include both old and new values
        DB::statement("ALTER TABLE donations MODIFY COLUMN donation_type ENUM('monetary','money','food','clothing','medical','medicine','other') NOT NULL");
        
        // Revert data back to old enum values
        DB::statement("UPDATE donations SET donation_type = 'monetary' WHERE donation_type = 'money'");
        DB::statement("UPDATE donations SET donation_type = 'medical' WHERE donation_type = 'medicine'");
        
        // Finally, revert enum to original values
        DB::statement("ALTER TABLE donations MODIFY COLUMN donation_type ENUM('monetary','food','clothing','medical','other') NOT NULL");
    }
};
