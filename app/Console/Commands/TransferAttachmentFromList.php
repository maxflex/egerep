<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class TransferAttachmentFromList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:subjects_from_lists';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer attachment subjects from lists';

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
        $attachments = DB::table('attachments')->where('subjects', '')->get();
        foreach ($attachments as $attachment) {
            \App\Models\Attachment::where('id', $attachment->id)->update([
                'subjects' => DB::table('request_lists')->where('id', $attachment->request_list_id)->value('subjects')
            ]);
        }
    }
}
