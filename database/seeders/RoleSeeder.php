<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['admin', 'marketing', 'formation', 'commercial', 'assistant'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $admin = User::factory()->create([
            'name' => 'Admin CAEI',
            'email' => 'admin@caei-afri.com',
            'password' => bcrypt('changeme123'),
        ]);
        $admin->assignRole('admin');
    }
}