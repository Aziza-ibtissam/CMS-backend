<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Conference;
use App\Models\Topic;
use App\Models\Subtopic;
use App\Models\User;
use Faker\Factory as Faker;

class InvitationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $conferences = Conference::all();
        $faker = Faker::create();

        // Fetch topics and subtopics
        $topics = Topic::pluck('name')->toArray();
        $subtopics = Subtopic::pluck('name')->toArray();
        
        // Fetch all users' emails
        $userEmails = User::pluck('email')->toArray();

        foreach ($conferences as $conference) {
            for ($i = 0; $i < 100; $i++) {
                DB::table('invitations')->insert([
                    'conference_id' => $conference->id,
                    'email' => $faker->randomElement($userEmails), // Use email from users table
                    'firstName' => $faker->firstName,
                    'lastName' => $faker->lastName,
                    'affiliation' => $faker->company,
                    'invitationStatus' => $faker->randomElement(['pending', 'accepted', 'declined']),
                    'reviewerTopic' => $faker->randomElement(array_merge($topics, $subtopics)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
