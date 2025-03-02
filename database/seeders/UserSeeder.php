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
                "name"              => "Administrator",
                "email"             => "admin@yukcoding.id",
                "username"          => "admin",
                "password"          => Hash::make("123456"),
                "role"              => 1,
                "information"       => "Admin Utama",
            ],
            [
                "id"                => 2,
                "name"              => "Agus",
                "email"             => "agus@yukcoding.id",
                "username"          => "agus",
                "password"          => Hash::make("123456"),
                "role"              => 2,
                "information"       => "Resepsionis 1",
            ],
            [
                "id"                => 3,
                "name"              => "Ujang",
                "email"             => "ujang@yukcoding.id",
                "username"          => "ujang",
                "password"          => Hash::make("123456"),
                "role"              => 2,
                "information"       => "Resepsionis 2",
            ],
        ];
        User::insert($data);
    }
}
