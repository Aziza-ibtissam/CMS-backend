<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Paper;
use App\Models\Conference;
use App\Models\User;
use Carbon\Carbon;

class PaperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Assuming you already have conferences and users seeded
        $conferences = Conference::all();
        $users = User::all(); // Get all users

        foreach ($conferences as $conference) {
            // Create papers for each conference
            for ($i = 0; $i < 10; $i++) { // Assuming you want to create 10 papers for each conference
                $paper = new Paper();
                $paper->conference_id = $conference->id;
                
                // Assign a random user to the paper
                $randomUser = $users->random();
                $paper->user_id = $randomUser->id;

                // You can set other attributes as needed
                $paper->paperTitle = 'Paper ' . ($i + 1); // Example paper title
                $paper->submitted_at = Carbon::now()->subDays(rand(1, 30)); // Random submission date
                $paper->paperfile = 'path/to/paperfile' . ($i + 1); // Example paper file path
                $paper->status = 'pending'; // Default status
                $paper->mark = 0; // Default mark

                // New fields
                $paper->authors = json_encode([
                    ['first_name' => 'Author', 'last_name' => 'One', 'email' => 'author1@example.com'],
                    ['first_name' => 'Author', 'last_name' => 'Two', 'email' => 'author2@example.com']
                ]); // Example authors with first and last names
                $paper->abstract = 'This is the abstract of Paper ' . ($i + 1); // Example abstract
                $paper->keywords = 'keyword' . ($i + 1); // Example keyword

                $paper->save();
            }
        }
    }
}
