<?php

namespace App\Jobs;

use DB;
use App\Jobs\Job;
use App\Models\Debt;
use App\Models\Account;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Service\Settings;

class UpdateDebtsTable extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    // по сколько запускать за шаг
    const STEP = 2000;

    /**
     * @int step – если идет полный пересчет, то выполняется в несколько шагов, на первом шаге таблица полностью очищается
     * @boolean is_last_step – последний шаг полного пересчета, обновить «время пересчета» и сумму дебета в Settings
     * @int tutor_id – пересчитываем для конкретного препода. в этом случае steps отсутствуют
     */
    private $params;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
     /**
      * Execute the console command.
      *
      * @return mixed
      */
     public function handle()
     {
         if (isset($this->params['step'])) {
             \Log::info("Step " . ($this->params['step'] + 1) . " starting...");
         }

         $query = DB::table('attachments')->where('forecast', '>', 0);

         if (isset($this->params['step'])) {
             // truncate on first step
             if ($this->params['step'] == 0) {
                 DB::table('debts')->truncate();
                 Settings::set('debt_updating', 1);
             }
             $query->skip($this->params['step'] * self::STEP)->take(self::STEP);
         }

         if (isset($this->params['tutor_id'])) {
             DB::table('debts')->where('tutor_id', $this->params['tutor_id'])->delete();
             $query->where('tutor_id', $this->params['tutor_id']);
         }

         $attachments = $query->get();
         $now = now(true);

         foreach($attachments as $attachment) {
             $query = DB::table('archives')->where('attachment_id', $attachment->id);
             // check if attachment exists
             if ($query->exists()) {
                 $date_end = $query->value('date');
                 $archive_date = $date_end;
             } else {
                 $date_end = $now;
                 $archive_date = null;
             }

             $date = $attachment->date;

             // дата последней встречи
             $last_account_date = Account::where('tutor_id', $attachment->tutor_id)->orderBy('date_end', 'desc')->value('date_end');
             $debtor = DB::table('tutors')->whereId($attachment->tutor_id)->value('debtor');

             while ($date < $date_end) {
                 DB::table('debts')->insert([
                     'date'      => $date,
                     'debt'      => $attachment->forecast / 7 * static::pissimisticCoef($date, $archive_date),
                     'tutor_id'  => $attachment->tutor_id,
                     'client_id' => $attachment->client_id,
                     'debtor'    => $debtor ? ($last_account_date === null ? $debtor : ($date >= $last_account_date)) : 0,
                     'after_last_meeting' => $last_account_date === null ? 1 : ($date >= $last_account_date),
                 ]);
                 $date = (new \DateTime($date))->modify('+1 day')->format('Y-m-d');
             }
         }
         // update on last step
         if (isset($this->params['is_last_step']) && $this->params['is_last_step'] === true) {
            Settings::set('debt_updated', now());
            Settings::set('debt_updating', 0);
         }

         if (isset($this->params['step'])) {
             \Log::info("Step " . ($this->params['step'] + 1) . " completed");
         }
     }

     /**
      * Писсимизирующий коэффициент
      */
     public static function pissimisticCoef($date, $archive_date)
     {
         // заархивирован этим летом?
         if ($archive_date !== null) {
             $archive_year = date('Y', strtotime($archive_date));
             $date_year = date('Y', strtotime($date));
             $archive_month_day = date('m-d', strtotime($archive_date));
             $summer_archive = (($archive_year == $date_year) && ($archive_month_day >= '06-01' && $archive_month_day <= '08-31'));
         } else {
             $summer_archive = false;
         }

         $date = date('m-d', strtotime($date));

         // первые 7 дней ноября
         if ($date >= '11-01' && $date <= '11-07') {
             return .6;
         }
         // последние 7 дней декабря 0,54
         if ($date >= '12-25' && $date <= '12-31') {
             return .54;
         }
         // первые 10 дней января - 0,12
         if ($date >= '01-01' && $date <= '01-10') {
             return .12;
         }
         // первые 14 дней мая - 0,54
         if ($date >= '05-01' && $date <= '05-14') {
             return .54;
         }
         // с 1 июня по 31 августа - 0,02 (или 0,72 если была архивации в летний период)
         if ($date >= '06-01' && $date <= '08-31') {
             return $summer_archive ? .72 : .02;
         }
         // остальные дни - 0,72
         return .72;
     }
}
