<?php

require_once 'vendor/autoload.php';

use App\Models\VolunteerTask;
use App\Models\User;
use App\Models\DistributionRecord;
use App\Models\Victim;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

// Simulate the Laravel application environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Simulating Task Completion Process\n";
echo "=================================\n\n";

try {
    // Start transaction
    DB::beginTransaction();
    
    // Find a pending volunteer task
    $task = VolunteerTask::where('status', 'pending')->first();
    
    if (!$task) {
        echo "No pending tasks found. Creating a test task...\n";
        
        // Create a test task
        $task = new VolunteerTask();
        $task->task_id = 'TASK-' . time();
        $task->assigned_by = 'admin123';
        $task->assigned_to = 'volunteer123';
        $task->task_title = 'Test Emergency Relief';
        $task->task_description = 'Deliver emergency supplies to affected family';
        $task->task_type = 'emergency_response';
        $task->priority = 'high';
        $task->victim_id = '2'; // Assuming victim with ID 2 exists
        $task->status = 'pending';
        $task->created_at = now();
        $task->save();
        
        echo "Created test task: " . $task->task_id . "\n";
    }
    
    echo "Processing task: " . $task->task_title . "\n";
    echo "Task type: " . $task->task_type . "\n";
    echo "Priority: " . $task->priority . "\n";
    echo "Victim ID: " . ($task->victim_id ?? 'None') . "\n\n";
    
    // Simulate task completion - this mimics the completeVolunteerTask method
    $distributionId = 'DIST-' . time();
    
    // Get victim information if available
    $victim = null;
    if ($task->victim_id) {
        $victim = Victim::where('victim_id', $task->victim_id)->first();
        if ($victim) {
            echo "Found victim: " . $victim->name . " at " . $victim->location . "\n";
        }
    }
    
    // Create distribution record with task information
    $distributionData = [
        'distribution_id' => $distributionId,
        'distribution_date' => now(),
        'volunteer_id' => $task->assigned_to,
        'victim_id' => $task->victim_id,
        'inventory_id' => null, // No specific inventory for this task
        'location' => $victim ? $victim->location : 'Task Location',
        'quantity' => 1,
        'task_type' => $task->task_type,
        'task_title' => $task->task_title,
        'task_description' => $task->task_description,
        'created_at' => now(),
        'updated_at' => now()
    ];
    
    DB::table('distribution_records')->insert($distributionData);
    echo "Created distribution record: " . $distributionId . "\n";
    
    // Update task status to completed
    $task->status = 'completed';
    $task->completed_date = now();
    $task->save();
    
    echo "Marked task as completed\n\n";
    
    // Test the distribution repository query
    echo "Testing distribution repository query after completion:\n";
    $distributions = DB::table('distribution_records')
        ->leftJoin('victims', 'distribution_records.victim_id', '=', 'victims.victim_id')
        ->leftJoin('inventory', 'distribution_records.inventory_id', '=', 'inventory.inventory_id')
        ->select(
            'distribution_records.*',
            'victims.name as victim_name',
            'victims.location',
            'inventory.item_name',
            'inventory.item_type'
        )
        ->where('distribution_records.volunteer_id', $task->assigned_to)
        ->orderBy('distribution_records.distribution_date', 'desc')
        ->limit(3)
        ->get();
    
    echo "Found " . $distributions->count() . " distribution records for volunteer " . $task->assigned_to . ":\n";
    
    foreach ($distributions as $dist) {
        echo "- ID: " . $dist->distribution_id . "\n";
        echo "  Task: " . ($dist->task_title ?? 'No task') . "\n";
        echo "  Type: " . ($dist->task_type ?? 'No type') . "\n";
        echo "  Victim: " . ($dist->victim_name ?? 'None') . "\n";
        echo "  Location: " . ($dist->location ?? 'No location') . "\n";
        echo "  Date: " . $dist->distribution_date . "\n\n";
    }
    
    // Rollback the transaction to not affect the actual database
    DB::rollback();
    echo "Transaction rolled back - no permanent changes made.\n";
    
} catch (Exception $e) {
    DB::rollback();
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}

echo "\nTest completed.\n";
