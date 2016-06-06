<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Service\PhoneDuplicate;
use App\Models\Client;
use App\Models\Tutor;
use App\Traits\Person;

class InitDuplicatesTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:duplicates_table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Init phone duplicates table';

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
        $this->info('Getting clients...');
        PhoneDuplicate::truncate();
        $clients = DB::table('clients')->where('phone', '<>', '')->get();

        $bar = $this->output->createProgressBar(count($clients));
        foreach ($clients as $client) {
            foreach(Person::$phone_fields as $phone_field) {
                $phone = $client->{$phone_field};
                if (! empty($phone)) {
                    // check if duplicate
                    if (Client::findByPhone($phone)->where('id', '<>', $client->id)->exists()) {
                        try {
                            PhoneDuplicate::add($phone, Client::ENTITY_TYPE);
                        }
                        catch (\Exception $e) {
                            // уникальные номера не будут добавляться
                        }
                    }
                }
            }
            $bar->advance();
        }
        $bar->finish();

        $this->info('Getting tutors...');
        $tutors = DB::table('tutors')->where('phone', '<>', '')->get();

        $bar = $this->output->createProgressBar(count($tutors));
        foreach ($tutors as $tutor) {
            foreach(Person::$phone_fields as $phone_field) {
                $phone = $tutor->{$phone_field};
                if (! empty($phone)) {
                    // check if duplicate
                    if (Tutor::findByPhone($phone)->where('id', '<>', $tutor->id)->exists()) {
                        try {
                            PhoneDuplicate::add($phone, Tutor::ENTITY_TYPE);
                        }
                        catch (\Exception $e) {
                            // уникальные номера не будут добавляться
                        }
                    }
                }
            }
            $bar->advance();
        }
        $bar->finish();
    }
}
