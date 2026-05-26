<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Super Admin
        User::firstOrCreate(
            ['phone' => '08000000001'],
            [
                'name'     => 'Super Admin',
                'email'    => 'superadmin@countrystore.test',
                'phone'    => '08000000001',
                'role'     => 'super_admin',
                'password' => bcrypt('password'),
            ]
        );

        // Admin
        User::firstOrCreate(
            ['phone' => '08000000002'],
            [
                'name'     => 'Store Admin',
                'email'    => 'admin@countrystore.test',
                'phone'    => '08000000002',
                'role'     => 'admin',
                'password' => bcrypt('password'),
            ]
        );

        // Staff / Cashier
        User::firstOrCreate(
            ['phone' => '08000000003'],
            [
                'name'     => 'Cashier One',
                'email'    => null,
                'phone'    => '08000000003',
                'role'     => 'staff',
                'password' => bcrypt('password'),
            ]
        );
    }
}
