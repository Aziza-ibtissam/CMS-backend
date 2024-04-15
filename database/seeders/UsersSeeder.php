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
            'username' => 'Super Admin',
            'password' => bcrypt('password123'),
            'firstName' => 'csm',
            'lastName' => 'amin',
            'phoneNumber' => '1234567890',
            'country' => 'ouargla',
            'affiliation' => 'ouargla',
            'dateOfBirth' =>'2023-08-15',
        ]);


        $superAdmin->assignRole('admin');
    }
}

