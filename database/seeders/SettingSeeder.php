<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('setting')->insert([
            'name_app' => 'UndanganQ',
            'color_bg_app' => '#6c3c0c',
            'send_whatsapp' => 1,
            'send_email' => 1,
            'greeting_page' => 1,
        ]);
    }
}
