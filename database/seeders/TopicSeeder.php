<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Topic; // Update the namespace as per your application
use App\Models\Conference;
use Faker\Factory;
class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $conferences = Conference::all();

        foreach ($conferences as $conference) {
            // Create topics for each conference
            for ($i = 0; $i < 5; $i++) { // Assuming you want to create 5 topics for each conference
                $topic = new Topic();
                $topic->conference_id = $conference->id;
                // You can set other attributes as needed
                $topic->name = 'Topic ' . ($i + 1); // Example name
                $topic->save();
            }
        }
    }
}
