<?php

namespace Database\Seeders;

use App\Models\Guest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GuestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('guest')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        for($i=1; $i<=20; $i++){
            Guest::create([
                "name_guest"            => fake()->name,
                "address_guest"         => fake()->address,
                "information_guest"     => fake()->jobTitle,
                "email_guest"           => fake()->email,
                "phone_guest"           => fake()->phoneNumber,
                "code_guest"            => Str::uuid(),
            ]);
        }
    }
}
