<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sms;
use App\Models\Email;
use Redis;

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
        $cache_key = 'sms_already_sent';

        // отправлять по
        $per_send = 100;

        // уже отослано
        $already_sent = Redis::get($cache_key) ?: 0;

        $text = "Частная школа ЕГЭ-Центра набирает 10-11 классников. Возможен экстернат. Подробнее на ege-centr.ru/private-school/, +7 (495) 646-85-92";
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
            Sms::send($phone, $message, false, Sms::SENDER_EC);
        }

        Redis::set($cache_key, $already_sent + $per_send);
    }
}
