<?php

namespace App\Console\Commands;

use App\Models\Service\AttachmentError;
use Illuminate\Console\Command;
use DB;
use App\Models\Service\Settings;
use App\Models\Attachment;
use App\Models\Review;

class ModelErrors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calc:model_errors {--attachments} {--reviews}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-find model errors';

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
        if ($this->option('attachments')) {
            $this->attachments();
        }
        if ($this->option('reviews')) {
            $this->reviews();
        }
    }

    public function attachments()
    {
        Settings::set('attachment_errors_updating', 1);

        $this->info('Getting attachments...');
        $attachments = Attachment::all();

        $bar = $this->output->createProgressBar(count($attachments));

        foreach ($attachments as $attachment) {
            DB::table('attachments')->where('id', $attachment->id)->update(['errors' => \App\Models\Helpers\Attachment::errors($attachment)]);
            $bar->advance();
        }
        Settings::set('attachment_errors_updated', now());
        Settings::set('attachment_errors_updating', 0);
        $bar->finish();
    }

    public function reviews()
    {
        $this->info('Getting reviews...');
        $reviews = Review::all();

        $bar = $this->output->createProgressBar(count($reviews));

        foreach ($reviews as $review) {
            DB::table('reviews')->where('id', $review->id)->update(['errors' => \App\Models\Helpers\Review::errors($review)]);
            $bar->advance();
        }
        $bar->finish();
    }
}
