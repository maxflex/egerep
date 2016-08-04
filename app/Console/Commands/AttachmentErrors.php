<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class AttachmentErrors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calc:attachment_errors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-find attachment errors';

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
        DB::table('attachment_errors')->truncate();

        $this->info('Getting attachments...');
        $attachments = \App\Models\Attachment::all();

        $bar = $this->output->createProgressBar(count($attachments));

        foreach ($attachments as $attachment) {
            $attachment_errors = $attachment->errors();
            if (count($attachment_errors)) {
                DB::table('attachment_errors')->insert([
                    'attachment_id' => $attachment->id,
                    'link'          => $attachment->link,
                    'codes'         => implode(',', $attachment_errors),
                ]);
            }
            $bar->advance();
        }
        Settings::set('attachment_errors_updated', now());
        $bar->finish();
    }
}
