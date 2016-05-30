<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attachment;

class AttachmentClientId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:attachment_client_id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer client_id from requests to attachments';

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
        $this->line('Starting transfer...');

        $attachments = Attachment::all();
        $bar = $this->output->createProgressBar($attachments->count());

        foreach ($attachments as $attachment) {
            $attachment->client_id = $attachment->requestList->request->client_id;
            $attachment->save();
            $bar->advance();
        }
        $bar->finish();
    }
}
