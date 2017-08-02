<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sms;
use App\Models\Email;

class SendSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send {start} {end}';

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
        // $this->info(getcwd());
        // отослано 400
        $text = "ЕГЭ-Центр информирует: запись на курсы ЕГЭ/ОГЭ заканчивается 5 августа. Тел.: +7 (495) 646-85-92";
        $phones = file_get_contents('phones.txt');
        $lines = array_slice(explode("\n", $phones), $this->argument('start'), $this->argument('end'));

        foreach($lines as $line) {
            @list($phone, $name) = explode("\t", $line);
            if ($name) {
                $greeting = $name . ', здравствуйте. ';
            } else {
                $greeting = 'Здравствуйте. ';
            }
            Sms::send($phone, $greeting . $text, false);
        }
    }
}
