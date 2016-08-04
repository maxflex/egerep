<?php

namespace App\Console\Commands;

use App\Models\Service\AttachmentError;
use Illuminate\Console\Command;
use DB;
use App\Models\Service\Settings;
use App\Models\Attachment;

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
        Settings::set('attachment_errors_updating', 1);

        $this->info('Getting attachments...');
        $attachments = Attachment::all();

        $bar = $this->output->createProgressBar(count($attachments));

        foreach ($attachments as $attachment) {
            $attachment_errors = $attachment->errors();
            if (count($attachment_errors)) {
                DB::table('attachment_errors')->insert(AttachmentError::prepareData($attachment, $attachment_errors));
            }
            $bar->advance();
        }
        Settings::set('attachment_errors_updated', now());
        Settings::set('attachment_errors_updating', 0);
        $bar->finish();
    }
}
