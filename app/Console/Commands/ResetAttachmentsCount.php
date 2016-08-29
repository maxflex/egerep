<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\AttachmentCountChanged;
use App\Models\Attachment;
use Storage;

class ResetAttachmentsCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:attachments_count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset attachments daily count';

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
        Storage::put(AttachmentCountChanged::COUNT_PLUS, Attachment::whereRaw('DATE(NOW()) = DATE(created_at)')->count());
        Storage::put(AttachmentCountChanged::COUNT_MINUS, 0);
    }
}
