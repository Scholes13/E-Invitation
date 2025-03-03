<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            
            [
                "id"                => 1,
                "name"              => "Pramuji",
                "email"             => "pramuji@example.com",
                "username"          => "pramuji",
                "password"          => Hash::make("bhayangkara1"),
                "role"              => 1,
                "information"       => "Admin Tambahan",
            ],
        ];
        User::insert($data);
    }
}
