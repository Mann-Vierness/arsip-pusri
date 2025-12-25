<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'BADGE' => 'ADMIN001',
            'Nama' => 'Administrator',
            'Password' => Hash::make('admin123'),
            'ROLE' => 'admin',
            'Departemen' => 'IT',
        ]);

        // Create Sample Users
        User::create([
            'BADGE' => 'USER001',
            'Nama' => 'Budi Santoso',
            'Password' => Hash::make('user123'),
            'ROLE' => 'user',
            'Departemen' => 'Produksi',
        ]);

        User::create([
            'BADGE' => 'USER002',
            'Nama' => 'Siti Aminah',
            'Password' => Hash::make('user123'),
            'ROLE' => 'user',
            'Departemen' => 'Keuangan',
        ]);

        User::create([
            'BADGE' => 'USER003',
            'Nama' => 'Ahmad Hidayat',
            'Password' => Hash::make('user123'),
            'ROLE' => 'user',
            'Departemen' => 'HRD',
        ]);

        $this->command->info('✅ Users seeded successfully!');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('Admin - Badge: ADMIN001, Password: admin123');
        $this->command->info('User  - Badge: USER001, Password: user123');
    }
}
