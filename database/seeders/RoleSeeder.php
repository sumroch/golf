<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'superadmin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'technician']);
        Role::create(['name' => 'director']);
        Role::create(['name' => 'chief']);
        Role::create(['name' => 'referee']);
        Role::create(['name' => 'observer']);
    }
}
