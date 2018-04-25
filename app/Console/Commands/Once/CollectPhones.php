<?php

namespace App\Console\Commands\Once;

use Illuminate\Console\Command;
use DB;

class CollectPhones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:collect_phones';

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
        // egecrm requests
        $result = DB::connection('egecrm')->select("
            select id, grade, name, phone, phone2, phone3, `date` from (select * from requests where grade!=11) r1
            where ((grade=10 and DATE(`date`) > '2017-04-01') or (grade=9 and DATE(`date`) > '2016-04-01')) and phone!='' and adding=0
        ");

        $phones = [];

        $bar = $this->output->createProgressBar(count($result));
        foreach($result as $r) {
            foreach(['phone', 'phone2', 'phone3'] as $field) {
                $phone = $r->{$field};
                if ($phone && $this->isMobilePhone($phone)) {
                    $phones[] = (object)[
                        'name' => $r->name,
                        'phone' => $phone,
                        'grade' => '',
                        'date' => '',
                    ];
                }
            }
            $bar->advance();
        }
        $bar->finish();

        // egecrm clients
        $result = DB::connection('egecrm')->select("
            select CONCAT(last_name, ' ',first_name , ' ', middle_name) as name, id_representative, phone, phone2, phone3
            from students where (grade!=11 and grade!=12)
        ");

        $bar = $this->output->createProgressBar(count($result));

        foreach($result as $r) {
            foreach(['phone', 'phone2', 'phone3'] as $field) {
                $phone = $r->{$field};
                if ($phone && $this->isMobilePhone($phone)) {
                    $phones[] = (object)[
                        'name' => $r->name,
                        'phone' => $phone,
                        'grade' => '',
                        'date' => '',
                    ];
                }
            }
            $representative = dbEgecrm('representatives')->whereId($r->id_representative)->first();
            if ($representative) {
                foreach(['phone', 'phone2', 'phone3'] as $field) {
                    $phone = $representative->{$field};
                    if ($phone && $this->isMobilePhone($phone)) {
                        $phones[] = (object)[
                            'name' => implode(' ', [$representative->last_name, $representative->first_name, $representative->middle_name]),
                            'phone' => $phone,
                            'grade' => '',
                            'date' => '',
                        ];
                    }
                }
            }
            $bar->advance();
        }
        $bar->finish();

        // egerep clients
        $result = DB::select("
            select name, phone, phone2, phone3, phone4, grade, r.created_at as `date`
            from clients c
            join requests r on r.client_id = c.id
            where (c.grade!=11 and c.grade!=12)
        ");

        $bar = $this->output->createProgressBar(count($result));

        foreach($result as $r) {
            foreach(['phone', 'phone2', 'phone3', 'phone4'] as $field) {
                $phone = $r->{$field};
                if ($phone && $this->isMobilePhone($phone)) {
                    $phones[] = (object)[
                        'name' => $r->name,
                        'phone' => $phone,
                        'grade' => $r->grade,
                        'date' => $r->date
                    ];
                }
            }
            $bar->advance();
        }

        $phone_counter = [];
        $text = "телефон\tимя\tкласс\tдата создания\n";
        foreach($phones as $phone) {
            if (in_array($phone->phone, $phone_counter)) {
                continue;
            }
            $text .= "{$phone->phone}\t{$phone->name}\t{$phone->grade}\t{$phone->date}\n";
            $phone_counter[] = $phone->phone;
        }
        \Storage::put('phones.tsv', $text);
    }

    private function isMobilePhone($phone)
    {
        return $phone[1] == '9';
    }
}
