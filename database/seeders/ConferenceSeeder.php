<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Conference;
use App\Models\User;
use Faker\Factory as Faker;

class ConferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Get existing user IDs
        $userIds = User::pluck('id')->toArray();

        // Categories and countries data
        $categories = [
            "Aeronautics",
            "Agriculture",
            "Anthropology",
            "Archaeology",
            "Astronomy",
            "Computer Science",
        ];

        $countries = [
            "Afghanistan",
            "Albania",
            "Algeria",
            "Andorra",
            "Angola",
        ];

        for ($i = 0; $i < 10; $i++) {
            // Choose a random user ID from existing users
            $randomUserId = $faker->randomElement($userIds);

            // Choose random category and country
            $category = $faker->randomElement($categories);
            $country = $faker->randomElement($countries);

            // Create conference
            $conference = Conference::create([
                'email' => $faker->email,
                'userID' => $randomUserId,
                'title' => $faker->sentence,
                'acronym' => $faker->word,
                'city' => $faker->city,
                'country' => $country,
                'webpage' => $faker->url,
                'category' => $category,
                'start_at' => $faker->dateTimeBetween('now', '+1 year'),
                'end_at' => $faker->dateTimeBetween('now', '+2 years'),
                'paper_subm_due_date' => $faker->dateTimeBetween('now', '+1 year'),
                'logo' => 'conferences/default.jpg', // Default logo path
            ]);

            // Define role
            $role = 'chair'; // You can set the role as needed

            // Attach user to conference with role
            $conference->users()->attach($randomUserId, ['role' => $role]);
        }
    }
}
