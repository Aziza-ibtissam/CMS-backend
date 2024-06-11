<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Conference;

class AutoAcceptConferences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'conferences:auto-accept';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically accept verified conferences';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get all conferences that are verified but not yet accepted
        $conferences = Conference::where('is_verified', 1)->where('is_accept', 2)->get();

        foreach ($conferences as $conference) {
            $conference->is_accept = 1;
            $conference->save();
            $this->info('Accepted conference: ' . $conference->title);
        }

        return 0;
    }
}
