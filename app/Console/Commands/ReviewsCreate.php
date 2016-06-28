<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Review;
use App\Models\Attachment;

class ReviewsCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:reviews';

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
        $attachments = Attachment::whereHas('archive', function($query) {
            $query->where('date', '<', '2014-08-01');
        })->get();

        $bar = $this->output->createProgressBar(count($attachments));

        foreach ($attachments as $attachment) {
            if (!$attachment->account_data_count && !$attachment->archive->total_lessons_missing) {
                \DB::table('reviews')->insert([
                    'attachment_id' => $attachment->id,
                    'user_id'       => 0,
                    'created_at'    => '2016-06-28 17:00:00',
                    'score'         => 11,
                    'state'         => 'unpublished',
                ]);
            }
            $bar->advance();
        }

        $bar->finish();
    }
}
