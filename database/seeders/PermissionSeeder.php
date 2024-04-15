<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role1 = Role::create(['name' => 'admin']);
        $role6 = Role::create(['name' => 'user']);
        $role2 = Role::create(['name' => 'chair']);
        $role3 = Role::create(['name' => 'reviewer']);
        $role4 = Role::create(['name' => 'author']);
    }
}
