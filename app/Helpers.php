<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

function myEvent() {
    $event = DB::table('event')->where('id_event', 1)->first();
    
    if (!$event) {
        // Log that no event was found with ID 1
        Log::warning('No event found with ID 1. This might cause errors where event properties are accessed.');
        
        // Check if there are any events in the table
        $anyEvent = DB::table('event')->first();
        if ($anyEvent) {
            // If there's at least one event, use that
            Log::info('Using event with ID ' . $anyEvent->id_event . ' as fallback.');
            return $anyEvent;
        }
        
        // No events found at all - create a default one
        Log::warning('No events found at all. Creating a default one.');
        $defaultId = DB::table('event')->insertGetId([
            'name_event' => 'Default Event',
            'type_event' => 'Default',
            'place_event' => 'Default Venue',
            'location_event' => 'Default Location',
            'start_event' => now(),
            'end_event' => now()->addHours(2),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return DB::table('event')->where('id_event', $defaultId)->first();
    }
    
    return $event;
}

function mySetting() {
    $setting = DB::table('setting')->first();
    
    if (!$setting) {
        // Log that no settings were found
        Log::warning('No settings found. This might cause errors where setting properties are accessed.');
        
        // Create a default setting record
        Log::warning('Creating default settings.');
        DB::table('setting')->insert([
            'name_app' => 'QR Scan App',
            'image_bg_status' => true,
            'send_email' => false,
            'send_whatsapp' => false,
            'greeting_page' => false,
            'enable_custom_qr' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return DB::table('setting')->first();
    }
    
    return $setting;
}

function decode_phone($number) {
    $chars = ['+', '(', ')', '-', ' '];
    $number = str_replace($chars, '', $number);
    if (substr($number, 0, 1) === '0') {
        $number = '62' . substr($number, 1);
    }
    return $number;
}