<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Paper;
use App\Models\User;
use Faker\Factory as Faker;

class AssignPaperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Assuming you already have papers and conferences seeded
        $papers = Paper::all();
        
        foreach ($papers as $paper) {
            $conference = $paper->conference; // Assuming Paper model has a relationship with Conference model

            // Retrieve reviewers who have the reviewer role for this conference
            $reviewers = $conference->users()->wherePivot('role', 'reviewer')->get();

            foreach ($reviewers as $reviewer) {
                // You can check if the reviewer is already assigned to this paper if needed

                // Assign the paper to the reviewer
                $answers = $this->generateAnswers(); // Generate random array of points
                $encodedAnswers = json_encode($answers); // Convert answers array to JSON

                $paper->reviewers()->attach($reviewer->id, [
                    'answers' => $encodedAnswers,
                    'finalDecision' => rand(-3, 3),
                    'isEligible' => rand(0, 1) ? 'yes' : 'no',
                    'comments' => $faker->paragraph(),
                    'confidentialRemarks' => rand(1, 10),
                ]);
            }
        }
    }

    /**
     * Generate a random array of points.
     *
     * @return array
     */
    private function generateAnswers()
    {
        $answers = [];
        for ($i = 0; $i < 5; $i++) { // Assuming 5 questions
            $answers[] = rand(0, 10); // Random integer between 0 and 10
        }
        return $answers;
    }
}
