<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Checking event table...\n";

if (Schema::hasTable('event')) {
    echo "Event table exists\n";
    
    // Check if there's any data in the event table
    $events = DB::table('event')->get();
    
    if ($events->count() > 0) {
        echo "Found " . $events->count() . " events\n";
        
        foreach ($events as $event) {
            echo "Event ID: " . $event->id_event . "\n";
            echo "Event Name: " . ($event->name_event ?? 'NULL') . "\n";
            echo "Event Type: " . ($event->type_event ?? 'NULL') . "\n";
            echo "Event Place: " . ($event->place_event ?? 'NULL') . "\n";
            echo "Event Location: " . ($event->location_event ?? 'NULL') . "\n";
            echo "Event Start: " . ($event->start_event ?? 'NULL') . "\n";
            echo "Event End: " . ($event->end_event ?? 'NULL') . "\n";
            echo "-------------------\n";
        }
    } else {
        echo "No events found\n";
    }
    
    // Check the myEvent() helper function
    echo "\nTesting myEvent() helper function:\n";
    try {
        $myEvent = myEvent();
        if ($myEvent) {
            echo "myEvent() returned an event with ID: " . $myEvent->id_event . "\n";
            echo "Event Type: " . ($myEvent->type_event ?? 'NULL') . "\n";
        } else {
            echo "myEvent() returned NULL\n";
        }
    } catch (Exception $e) {
        echo "Error calling myEvent(): " . $e->getMessage() . "\n";
    }
    
} else {
    echo "Event table does not exist\n";
}

echo "\nDone checking event table\n"; 