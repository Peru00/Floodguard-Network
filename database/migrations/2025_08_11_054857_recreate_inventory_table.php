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
        // Drop the existing inventory table
        Schema::dropIfExists('inventory');
        
        // Create the proper inventory table
        Schema::create('inventory', function (Blueprint $table) {
            $table->string('inventory_id', 20)->primary();
            $table->enum('item_type', ['food', 'clothing', 'medical', 'other']);
            $table->integer('quantity');
            $table->string('item_name', 255);
            $table->text('item_description')->nullable();
            $table->datetime('added_date')->default(now());
            $table->string('source_donation_id', 20)->nullable();
            $table->enum('status', ['available', 'reserved', 'distributed'])->default('available');
            $table->date('expiry_date')->nullable()->comment('Expiry date for perishable items');
            $table->timestamps();
            
            // Add foreign key constraint
            $table->foreign('source_donation_id')->references('donation_id')->on('donations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
        
        // Recreate the old empty inventory table
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
};
