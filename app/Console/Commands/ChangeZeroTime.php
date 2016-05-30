<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ChangeZeroTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:zero_time';

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
        $this->info('Starting...');

        $attachments = Attachment::whereRaw("created_at LIKE '%00:00:00%'")->get();

        foreach ($attachments as $attachment) {
            $new_date = date('Y-m-d', strtotime($attachment->getClean('created_at')));

            Attachment::where('id', $attachment->id)->update([
                'created_at' => $new_date .' 00:00:00',
                'updated_at' => $new_date .' 00:00:00',
            ]);
        }
    }
}
