<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Topic;
use App\Models\Conference;
use Faker\Factory as Faker;

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
        $faker = Faker::create();

        foreach ($conferences as $conference) {
            $topic = new Topic();
            $topic->conference_id = $conference->id;
            $topic->name = ucfirst($faker->word); // Example: 'Artificial Intelligence'
            $topic->save();
        }
    }
}
