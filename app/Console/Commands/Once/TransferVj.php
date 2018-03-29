<?php

namespace App\Console\Commands\Once;

use Illuminate\Console\Command;

class TransferVj extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:vj_transfer';

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
        $planned = dbEgecrm('group_schedule')->where('id_group', '>', 0)
                    ->where('date', '>', now(true))->get();

        $this->info('Planned lessons...');
        $bar = $this->output->createProgressBar(count($planned));
        foreach($planned as $p) {
            dbEgecrm('visit_journal')->insert([
                'id_group' => $p->id_group,
                'lesson_date' => $p->date,
                'lesson_time' => $p->time,
                'cabinet' => $p->cabinet,
                'is_free' => $p->is_free,
                'cancelled' => $p->cancelled,
                'year' => null,
            ]);
            $bar->advance();
        }
        $bar->finish();

        //
        //  entry id
        //
        $this->line('Setting entry ids...');
        $lessons = dbEgecrm('visit_journal')->where('type_entity', 'TEACHER')->orWhere('type_entity', null)->get();

        $bar = $this->output->createProgressBar(count($lessons));

        foreach($lessons as $lesson) {
            dbEgecrm('visit_journal')
                ->where('id_group', $lesson->id_group)
                ->where('lesson_date', $lesson->lesson_date)
                ->where('lesson_time', $lesson->lesson_time)
                ->update([
                    'entry_id' => $lesson->id
                ]);
            $bar->advance();
        }
        $bar->finish();

        //
        // vacations
        //

        $this->line('Vacations...');
        $vacations = dbEgecrm('group_schedule')->select('date')->where('id_group', 0)->get();

        $bar = $this->output->createProgressBar(count($vacations));

        foreach($vacations as $vacation) {
            dbEgecrm('vacations')->insert([
                'date' => $vacation->date,
                'year' => self::academicYear($vacation->date)
            ]);
            $bar->advance();
        }

        $this->line('Cancelled & free...');
        $data = dbEgecrm('group_schedule')->where('cancelled', 1)->get();

        foreach($data as $d) {
            dbEgecrm('visit_journal')
                ->where('id_group', $d->id_group)
                ->where('lesson_date', $d->date)
                ->where('lesson_time', $d->time)
                ->update(['cancelled' => 1]);
        }

        $data = dbEgecrm('group_schedule')->where('is_free', 1)->get();

        foreach($data as $d) {
            dbEgecrm('visit_journal')
                ->where('id_group', $d->id_group)
                ->where('lesson_date', $d->date)
                ->where('lesson_time', $d->time)
                ->update(['is_free' => 1]);
        }
    }


    private static function academicYear($date)
	{
		$year = date("Y", strtotime($date));
		$day_month = date("m-d", strtotime($date));

		if ($day_month >= '01-01' && $day_month <= '07-15') {
			$year--;
		}
		return $year;
	}
}
