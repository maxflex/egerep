<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CallTwoDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:two_days';

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
        $attachments = \DB::table('attachments')->where('called', 1)->get();

        foreach ($attachments as $attachment) {
            $d = new \DateTime(strtotime($attachment->date));
            $d->modify('+2 days');
            \App\Models\Notification::create([
                'entity_id'   => $attachment->id,
                'entity_type' => 'attachment',
                'comment'     => 'узнать как идет процесс',
                'approved'    => true,
                'date'        => $d->format('Y-m-d')
            ]);
        }
    }
}
