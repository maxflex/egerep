<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attachment;

class DeleteNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:delete_notifications';

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
        $attachments = Attachment::archivedOrActive()->where('date', '<=', '2016-06-01')->get();

        $bar = $this->output->createProgressBar();

        foreach ($attachments as $a) {
            \DB::table('notifications')->where('entity_id', $a->id)->where('entity_type', 'attachment')->delete();
            $bar->advance();
        }
    }
}
