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
        $topics = [
            'Computer Science',
            'Data Science',
            'Artificial Intelligence',
            'Cybersecurity',
            'Software Engineering',
            'Networks',
            'Information Systems',
            'Human-Computer Interaction',
            'Bioinformatics',
            'Robotics'
        ];

        foreach ($conferences as $conference) {
            foreach ($topics as $topicName) {
                $topic = new Topic();
                $topic->conference_id = $conference->id;
                $topic->name = $topicName;
                $topic->save();
            }
        }
    }
}