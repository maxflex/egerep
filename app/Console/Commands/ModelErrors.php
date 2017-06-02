<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Service\Settings;
use App\Models\Attachment;
use App\Models\Review;
use App\Models\Tutor;

class ModelErrors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calc:model_errors {--attachments} {--reviews} {--tutors} {--requests} {--accounts}';

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

        if ($this->option('tutors')) {
            $this->tutors();
        }

        if ($this->option('requests')) {
            $this->requests();
        }

        if ($this->option('accounts')) {
            $this->accounts();
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
        Settings::set('review_errors_updating', 1);
        $this->info('Getting reviews...');
        $reviews = Review::all();

        $bar = $this->output->createProgressBar(count($reviews));

        foreach ($reviews as $review) {
            DB::table('reviews')->where('id', $review->id)->update(['errors' => \App\Models\Helpers\Review::errors($review)]);
            $bar->advance();
        }
        Settings::set('review_errors_updated', now());
        Settings::set('review_errors_updating', 0);
        $bar->finish();
    }

    public function tutors()
    {
        Settings::set('tutor_errors_updating', 1);
        $this->info('Getting tutors...');
        $tutors = Tutor::all();

        $bar = $this->output->createProgressBar(count($tutors));

        foreach ($tutors as $tutor) {
            DB::table('tutors')->where('id', $tutor->id)->update(['errors' => \App\Models\Helpers\Tutor::errors($tutor)]);
            $bar->advance();
        }

        Settings::set('tutor_errors_updated', now());
        Settings::set('tutor_errors_updating', 0);
        $bar->finish();
    }

    public function requests()
    {
        Settings::set('request_errors_updating', 1);
        $this->info('Getting requests...');
        $requests = Request::all();

        $bar = $this->output->createProgressBar(count($requests));

        foreach ($requests as $request) {
            DB::table('requests')->where('id', $request->id)->update(['errors' => \App\Models\Helpers\Request::errors($request)]);
            $bar->advance();
        }

        Settings::set('request_errors_updated', now());
        Settings::set('request_errors_updating', 0);
        $bar->finish();
    }

    public function accounts()
    {
        Settings::set('account_errors_updating', 1);
        $this->info('Getting accounts...');
        $accounts = Account::all();

        $bar = $this->output->createProgressBar(count($accounts));

        foreach ($accounts as $account) {
            DB::table('accounts')->where('id', $account->id)->update(['errors' => \App\Models\Helpers\Account::errors($account)]);
            $bar->advance();
        }

        Settings::set('account_errors_updated', now());
        Settings::set('account_errors_updating', 0);
        $bar->finish();
    }
}
