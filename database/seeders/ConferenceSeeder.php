<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Conference;
use App\Models\Form;
use App\Models\User;
use App\Models\Question;
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

        // Get all user IDs
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

        // Number of conferences to create
        $numConferences = 25;

for ($i = 0; $i < $numConferences; $i++) {
    // Choose random category and country
    $category = $faker->randomElement($categories);
    $country = $faker->randomElement($countries);
    $userId = $faker->randomElement($userIds);

    // Create conference
    $conference = Conference::create([
        'userID' => $userId,
        'email' => $faker->email,
        'title' => $faker->sentence,
        'acronym' => $faker->word,
        'city' => $faker->city,
        'country' => $country,
        'webpage' => $faker->url,
        'category' => $category,
        'start_at' => $faker->dateTimeBetween('now', '+1 year'),
        'end_at' => $faker->dateTimeBetween('now', '+2 years'),
        'review_due_date' => $faker->dateTimeBetween('now', '+2 years'),
        'paper_subm_due_date' => $faker->dateTimeBetween('now', '+1 year'),
        'logo' => '/storage/logos/lBg5HCKmQcxr3uK2v5FEyro6mj3LWOW90uiDXEpV.jpg', // Default logo path
    ]);

    // Assign a user with the role of "chair" to the conference
    $chairUserId = $faker->randomElement($userIds);
    $conference->users()->attach($chairUserId, ['role' => 'chair']);
   /*
    // Create form for the conference
    $form = Form::create([
        'conference_id' => $conference->id,
        'finalDecisionCoefficient' => $faker->numberBetween(1, 10),
        'confidentialRemarksCoefficient' => $faker->numberBetween(1, 10),
        'eligibleCoefficient' => $faker->numberBetween(1, 10),
    ]);

    // Generate random number of questions for the form
    $numQuestions = $faker->numberBetween(1, 10); // Adjust as needed
    for ($j = 0; $j < $numQuestions; $j++) {
        Question::create([
            'form_id' => $form->id,
            'description' => $faker->sentence,
            'coefficient' => $faker->numberBetween(1, 10),
            'point' => $faker->numberBetween(1, 10),
        ]);
    }
    */

    // Shuffle user IDs array to randomize reviewer assignment
    shuffle($userIds);

    // Select random reviewers (up to 10% of total users) for this conference
    $numReviewers = max(1, count($userIds) / 10); // Ensure at least one reviewer
    $reviewerIds = array_slice($userIds, 0, $numReviewers);

    // Assign reviewers to the conference
    foreach ($reviewerIds as $reviewerId) {
        $conference->users()->attach($reviewerId, ['role' => 'reviewer']);
    }
}
    }
}
