<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use DB;
use App\Http\Requests;
use App\Models\Tutor;
use App\Models\Account;
use App\Models\Service\Settings;


class DebtRecalc extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($tutor_id = null)
    {

        $query = Account::query();

        if ($tutor_id) {
            $query->where('tutor_id', $tutor_id);
        }

        $accounts = $query->get();

        foreach ($accounts as $account) {
            static::recalcDebt($account->date_start, $account->date_end, $account->tutor_id, $account->id);
        }

        // Преподаватели с клиентами, но без встреч
        $query = DB::table('tutors');

        if ($tutor_id) {
            $query->where('tutors.id', $tutor_id);
        }

        // #1082 сначала очищаем значения
        $update_query = clone $query;
        $update_query->update([
            'debt_calc' => 0,
        ]);

        $query->join('attachments', 'attachments.tutor_id', '=', 'tutors.id')
            ->leftJoin('accounts', 'accounts.tutor_id', '=', 'tutors.id')
            ->whereNull('accounts.id')
            ->orderBy('attachments.date', 'asc')
            ->select('attachments.tutor_id', 'attachments.date')
            ->groupBy('attachments.tutor_id');

        $no_accounts = $query->get();

        foreach ($no_accounts as $account) {
            static::recalcDebt($account->date, now(true), $account->tutor_id);
        }

        if (! $tutor_id) {
            Settings::set('debt_updated', now());
        }
    }


    /**
     * Пересчитать расчетный дебет
     *
     * $account_id – если не указан, то поседний период ... сегодня (хранится в таблице преподавателей)
     */
    public static function recalcDebt($date_start, $date_end, $tutor_id, $account_id = false)
    {
        // Получить клиентов, соответствующих периоду
        $data = DB::table('attachments')->leftJoin('archives', 'archives.attachment_id', '=', 'attachments.id')
            ->where('attachments.tutor_id', $tutor_id)
            ->where('attachments.date', '<=', $date_end)
            ->where(function($query) use ($date_start) {
                $query->where('archives.id', null)
                      ->orWhere('archives.date', '>', $date_start); // может >= ?
            })
            ->select(DB::raw('
                attachments.forecast,
                attachments.client_id,
                attachments.date as attachment_date,
                archives.date as archive_date'))
            ->get();

        // Пересчитываем
        $debt = 0;

        foreach (dateRange($date_start, $date_end) as $date) {
            foreach ($data as $d) {
                $coef = static::pissimisticCoef($date, $d->archive_date);
                if (($d->attachment_date <= $date) && (!$d->archive_date || $d->archive_date >= $date)) {
                    $debt += ($d->forecast / 7) * $coef;
                }
            }
        }

        if (! $account_id) {
            Tutor::where('id', $tutor_id)->update([
                'debt_calc'     => $debt,
                'debt_updated'  => now(),
            ]);
        } else {
            Account::where('id', $account_id)->update([
                'debt_calc' => $debt
            ]);
            // если это последний период, то обновить в промежутке
            // поседний период ... сегодня
            if ($account_id == Account::where('tutor_id', $tutor_id)->take(1)->orderBy('date_end', 'desc')->value('id')) {
                // @todo: нужно проверить ситуацию, когда конец периода = сегодня
                static::recalcDebt($date_end, now(true), $tutor_id);
            }
        }
    }

    /**
     * Писсимизирующий коэффициент
     */
    public static function pissimisticCoef($date, $archive_date)
    {
        // заархивирован этим летом?
        if ($archive_date) {
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
        // с 1 июня по 20 августа - 0,02 (или 0,72 если была архивации в летный период)
        if ($date >= '06-01' && $date <= '08-20') {
            return $summer_archive ? .72 : .02;
        }
        // с 21 августа по 31 августа - 0,1 (или 0,72 если была архивации в летный период)
        if ($date >= '08-21' && $date <= '08-31') {
            return $summer_archive ? .72 : .1;
        }
        // остальные дни - 0,72
        return .72;
    }


    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
