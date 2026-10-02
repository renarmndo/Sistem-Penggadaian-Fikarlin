<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Default password for all demo users.
     */
    private const DEFAULT_PASSWORD = 'password';

    /**
     * Seed the application's database with demo users for each role.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Owner Buulolo',
                'email' => 'owner@buulolo.id',
                'role' => User::ROLE_OWNER,
                'is_active' => true,
            ],
            [
                'name' => 'Admin Buulolo',
                'email' => 'admin@buulolo.id',
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
            ],
            [
                'name' => 'Petugas Buulolo',
                'email' => 'petugas@buulolo.id',
                'role' => User::ROLE_PETUGAS,
                'is_active' => true,
            ],
            [
                'name' => 'Petugas Nonaktif',
                'email' => 'inactive@buulolo.id',
                'role' => User::ROLE_PETUGAS,
                'is_active' => false,
            ],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make(self::DEFAULT_PASSWORD),
                'role' => $user['role'],
                'is_active' => $user['is_active'],
            ]);
        }
    }
}
