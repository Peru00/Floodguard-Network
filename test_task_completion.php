<?php

require_once 'vendor/autoload.php';

use App\Models\VolunteerTask;
use App\Models\User;
use App\Models\DistributionRecord;
use Illuminate\Support\Facades\DB;

// Simulate the Laravel application environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Task Completion and Distribution Record Creation\n";
echo "====================================================\n\n";

try {
    // Check if we have any volunteer tasks
    $tasks = VolunteerTask::where('status', 'pending')->get();
    echo "Pending volunteer tasks: " . $tasks->count() . "\n";
    
    if ($tasks->count() > 0) {
        $task = $tasks->first();
        echo "Sample task: " . $task->task_title . "\n";
        echo "Task type: " . $task->task_type . "\n";
        echo "Task description: " . $task->task_description . "\n";
        echo "Victim ID: " . ($task->victim_id ?? 'None') . "\n\n";
    }
    
    // Check distribution records structure
    echo "Checking distribution_records table structure:\n";
    $columns = DB::select("SHOW COLUMNS FROM distribution_records");
    foreach ($columns as $column) {
        echo "- " . $column->Field . " (" . $column->Type . ")\n";
    }
    echo "\n";
    
    // Count current distribution records
    $distributionCount = DistributionRecord::count();
    echo "Current distribution records: " . $distributionCount . "\n\n";
    
    // Test the distribution repository query
    echo "Testing distribution repository query:\n";
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
        ->orderBy('distribution_records.distribution_date', 'desc')
        ->limit(5)
        ->get();
    
    echo "Found " . $distributions->count() . " distribution records\n";
    
    foreach ($distributions as $dist) {
        echo "- ID: " . $dist->distribution_id . 
             ", Task: " . ($dist->task_title ?? 'No task') . 
             ", Type: " . ($dist->task_type ?? 'No type') . 
             ", Victim: " . ($dist->victim_name ?? 'None') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}

echo "\nTest completed.\n";
