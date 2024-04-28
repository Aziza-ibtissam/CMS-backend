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

        $numberOfUsers = 5;

// Loop to create users
for ($i = 1; $i <= $numberOfUsers; $i++) {
    // Create user
    $user = User::firstOrCreate([
        'email' => "user{$i}@example.com",
    ], [
        'password' => bcrypt('password123'),
        'firstName' => 'User',
        'lastName' => " {$i}",
        'country' => 'Example Country',
        'affiliation' => 'Example Affiliation',
    ]);

    // Assign "user" role to the user
    $user->assignRole('user');
}
    }
}

