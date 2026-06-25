<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@fixflow.test'],
            [
                'name' => 'Admin User',
                'role' => User::ROLE_ADMIN,
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'technician@fixflow.test'],
            [
                'name' => 'Mike Torres',
                'role' => User::ROLE_TECHNICIAN,
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@fixflow.test'],
            [
                'name' => 'John Customer',
                'role' => User::ROLE_CUSTOMER,
                'password' => Hash::make('password'),
            ]
        );
    }
}
