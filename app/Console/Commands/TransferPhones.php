<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\Person;
use DB;

class TransferPhones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:phones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer tutor phones to separate PHONES table';

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
        DB::table('phones')->truncate();
        $items = DB::table('tutors')->select('id', 'phone', 'phone2', 'phone3', 'phone4')->get();

        $bar = $this->output->createProgressBar(count($items));

        foreach($items as $item) {
            foreach(Person::$phone_fields as $field) {
                $phone = $item->{$field};
                if (! empty(trim($phone))) {
                    try {
                        DB::table('phones')->insert([
                            'phone' => $phone,
                            'tutor_id' => $item->id
                        ]);
                    } catch (\Exception $e) { }
                }
            }
            $bar->advance();
        }
        $bar->finish();
    }
}
