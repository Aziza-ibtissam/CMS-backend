<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $this->call(UsersSeeder::class);
        //$this->call(ConferenceSeeder::class);
        //$this->call(TopicSeeder::class);
        //$this->call(SubtopicSeeder::class);
       // $this->call(PaperSeeder::class);
       // $this->call(InvitationsSeeder::class);
        //$this->call(AssignPaperSeeder::class);


    }
}
