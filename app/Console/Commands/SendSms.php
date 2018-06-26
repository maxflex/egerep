<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sms;
use App\Models\Email;
use Cache;

class SendSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send {start?} {end?}';

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

        // отправлять по
        $per_send = 3;

        // сколько хранить в кеше
        $keep_for = 60 * 24 * 7;

        // уже отослано
        $already_sent = Cache::remember('sms_already_sent', $keep_for, function() {
            return 0;
        });
        $this->info($already_sent);

        $text = "ЕГЭ-Центр ведет набор 10-11 классников в школу с уклоном на подготовку к ЕГЭ. Возможен экстернат. Подробности на ege-centr.ru или по тел.: +7 (495) 646-85-92";
        $phones = file_get_contents('phones.tsv');
        // $lines = array_slice(explode("\n", $phones), $this->argument('start'), $this->argument('end'));
        $lines = array_slice(explode("\n", $phones), $already_sent, $per_send);

        foreach($lines as $line) {
            @list($phone, $name) = explode("\t", $line);
            if ($name) {
                $greeting = $name . ', здравствуйте. ';
            } else {
                $greeting = 'Здравствуйте. ';
            }
            $message = $greeting . $text;
            Sms::send($phone, $message, false);
        }

        Cache::put('sms_already_sent', $already_sent + $per_send);
    }
}
