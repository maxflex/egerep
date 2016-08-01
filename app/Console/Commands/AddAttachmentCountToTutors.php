<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use App\Models\Tutor;
use Illuminate\Console\Command;

class AddAttachmentCountToTutors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutor:countattachments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('Starting...');

        $tutors = Tutor::all();
        
        foreach ($tutors as $tutor) {
            $tutor->attachments_count = Attachment::where('tutor_id', $tutor->id)->count();
            $tutor->save();
        }
    }
}
