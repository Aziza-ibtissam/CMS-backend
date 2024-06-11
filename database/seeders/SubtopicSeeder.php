<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subtopic; // Update the namespace as per your application
use App\Models\Topic;

class SubtopicSeeder extends Seeder {
    public function run() {
        $topicSubtopics = [
            'Computer Science' => ['Algorithms', 'Data Structures', 'Operating Systems'],
            'Data Science' => ['Machine Learning', 'Data Mining', 'Big Data'],
            'Artificial Intelligence' => ['Neural Networks', 'Natural Language Processing', 'Computer Vision'],
            'Cybersecurity' => ['Network Security', 'Cryptography', 'Risk Management'],
            'Software Engineering' => ['Software Development Life Cycle', 'Agile Methodologies', 'Software Testing'],
            'Networks' => ['Network Protocols', 'Wireless Networks', 'Network Architecture'],
            'Information Systems' => ['Database Management', 'Enterprise Systems', 'Information Security'],
            'Human-Computer Interaction' => ['User Interface Design', 'Usability Testing', 'Interaction Design'],
            'Bioinformatics' => ['Genomics', 'Proteomics', 'Computational Biology'],
            'Robotics' => ['Autonomous Systems', 'Robot Kinematics', 'Control Systems']
        ];

        foreach ($topicSubtopics as $topicName => $subtopics) {
            $topic = Topic::where('name', $topicName)->first();
            if ($topic) {
                foreach ($subtopics as $subtopicName) {
                    Subtopic::create([
                        'topic_id' => $topic->id,
                        'name' => $subtopicName
                    ]);
                }
            }
        }
    }
}