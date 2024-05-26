<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subtopic; // Update the namespace as per your application
use App\Models\Topic;

class SubtopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $topics = Topic::all();

        foreach ($topics as $topic) {
            // Create subtopics for each topic
            for ($i = 0; $i < 3; $i++) { // Assuming you want to create 3 subtopics for each topic
                $subtopic = new Subtopic();
                $subtopic->topic_id = $topic->id;
                // You can set other attributes as needed
                $subtopic->name = 'Subtopic ' . ($i + 1); // Example name
                $subtopic->save();
            }
        }
    }
}
