<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sms;

class SecuritySms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:notify';

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
		// получить всех преподавателей, у которых дебет больше 2000
        // и которым не было отправлено смс за последний месяц
        $tutors = \DB::table('tutors')
            ->whereRaw('((security_sms_date < DATE(NOW() - INTERVAL 30 DAY)) or (security_sms_date is null))')
            ->whereRaw("(SELECT SUM(debt) FROM debts WHERE tutor_id = tutors.id AND after_last_meeting = 1) > 2000")
            ->get();

        $template = dbEgecrm('templates')->where('number', 19)->value('text');

        foreach($tutors as $tutor) {
            $message = str_replace('{tutor_name}', "{$tutor->first_name} {$tutor->last_name}", $template);
            throughNumbers($tutor, function($number) use ($message) {
                Sms::send($number, $message, 'SECRET');
            });
            \DB::table('tutors')->whereId($tutor->id)->update([
                'security_sms_date' => now(true),
            ]);
        }
    }
}
