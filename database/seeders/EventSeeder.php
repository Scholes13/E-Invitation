<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Event::create([
            "name_event"        => "Ruby Json & Vue Perl",
            "type_event"        => "Undangan Pernikahan",
            "place_event"       => "Gedung Serba Guna",
            "location_event"    => "Jl. Pemuda No. 1, Pati, Jateng",
            "start_event"       => "2026-08-10 09:00:00",
            "end_event"         => "2026-08-10 21:00:00",
            "information_event" => "Terimakasih banyak atas kehadirannya :)",
            'code_event'        => Str::random(6),
            'color_bg_event'    => '#6c3c0c',
            'color_text_event'    => '#e3eaef',
        ]);
    }
}
