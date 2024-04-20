<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $superAdmin = User::firstOrCreate([
            'email' => 'cmsadmin@gmail.com',
        ], [
            'password' => bcrypt('password123'),
            'firstName' => 'csm',
            'lastName' => 'amin',
            'country' => 'ouargla',
            'affiliation' => 'ouargla',
        ]);


        $superAdmin->assignRole('admin');
    }
}

