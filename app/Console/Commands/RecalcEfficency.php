<?php

namespace App\Console\Commands;

use App\Jobs\RecalcUserEfficency;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Service\Settings;
use App\Models\Account;

class RecalcEfficency extends Command
{
    // дата самой первой заявки
    const FIRST_REQUEST_DATE = '2007-08-30';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recalc:efficency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'recalc efficency table';

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
        \DB::table('efficency_data')->truncate();

        // системный пользователь тоже нужен, так как много заявок остаются без ответственного
        $user_ids = array_merge([0], \App\Models\User::real()->pluck('id')->all());
        Settings::set('efficency_updating', 1);


        $day = Carbon::today();

        // разница в днях между первой заявкой и сегодня – и есть полоса прогресса
        $bar = $this->output->createProgressBar($day->diffInDays(Carbon::parse(self::FIRST_REQUEST_DATE)));

        while ($day->toDateString() > self::FIRST_REQUEST_DATE) {
            foreach($user_ids as $user_id) {
                // обязательно обнуляем массив
                $data = [];
                $conversion_denominator = 0;

                $request_query = \App\Models\Request::query();
                $attachments_query = \App\Models\Attachment::query();

                $request_query->whereDate('requests.created_at', '=', $day->toDateString());
                $attachments_query->where('attachments.date', $day->toDateString());


                $request_query->where('requests.user_id', $user_id);
                $attachments_query->where('attachments.user_id', $user_id);

                $data['requests_total'] = $request_query->count();

                if ($data['requests_total'] > 0) {
                    foreach (\App\Models\Request::$states as $request_state) {
                        $data['requests_' . $request_state] = static::cloneQuery($request_query)->searchByState($request_state)->count();
                    }
                    if (isset($data['requests_deny'])) {
                        $conversion_denominator += $data['requests_deny'];
                    }
                }


                $data['attachments_total'] = static::cloneQuery($attachments_query)->count();
                if ($data['attachments_total'] > 0) {
                    $data['attachments_newest'] = static::cloneQuery($attachments_query)->newest()->count();
                    $data['attachments_active'] = static::cloneQuery($attachments_query)->active()->count();
                    $data['attachments_archived_no_lessons'] = static::cloneQuery($attachments_query)->archived()->hasLessonsWithMissing('=0')->count();
                    $data['attachments_archived_one_lesson'] = static::cloneQuery($attachments_query)->archived()->hasLessonsWithMissing('=1')->count();
                    $data['attachments_archived_two_lessons'] = static::cloneQuery($attachments_query)->archived()->hasLessonsWithMissing('=2')->count();
                    $data['attachments_archived_three_or_more_lessons'] = static::cloneQuery($attachments_query)->archived()->hasLessonsWithMissing('>=3')->count();

                    $data['forecast'] = static::cloneQuery($attachments_query)->active()->sum('forecast') + static::cloneQuery($attachments_query)->archived()->hasLessonsWithMissing('>=3')->sum('forecast');

                    // коммиссия стыковок
                    $data['commission'] = static::cloneQuery($attachments_query)->without(['archive', 'review'])
                                                        ->select(\DB::raw('round(sum(if(commission > 0, commission, ' . Account::DEFAULT_COMMISSION . ' * sum))) as `sum`'))
                                                        ->join('account_datas', function($join) {
                                                            $join->on('attachments.tutor_id', '=', 'account_datas.tutor_id')
                                                                 ->on('attachments.client_id', '=', 'account_datas.client_id');
                                                        })->value('sum') ?: 0;

                    // эффективность
                    $attachments_with_request_list = static::cloneQuery($attachments_query)->join('request_lists as rl', 'request_list_id', '=', 'rl.id')->without(['review', 'archive']);

                    // request => user_attachment_count
                    $request_attachments_count = static::cloneQuery($attachments_with_request_list)
                                                                    ->groupBy('request_id')
                                                                    ->select('request_id', \DB::raw('count(attachments.id) as attachments_count'))
                                                                    ->get()->keyBy('request_id')->all();
                    $request_ids = array_keys($request_attachments_count);
                    // request => attachment_count
                    $request_attachments_count_without_users = \App\Models\RequestList::whereIn('request_id', $request_ids)
                                                                    ->join('attachments as a', 'a.request_list_id', '=', 'request_lists.id')
                                                                    ->groupBy('request_id')
                                                                    ->select(['request_id', \DB::raw('count(a.id) as attachments_count')])
                                                                    ->get()->keyBy('request_id')->all();

                    foreach ($request_ids as $request_id) {
                        $numerator   = $request_attachments_count[$request_id]->attachments_count;
                        $denominator = $request_attachments_count_without_users[$request_id]->attachments_count;
                        $conversion_denominator += $numerator / $denominator;
                    }
                    if (isset($data['requests_deny'])) {
                        $conversion_denominator += $data['requests_deny'];
                    }
                }

                if ($data['requests_total'] || $data['attachments_total']) {
                    $data['date'] = $day->toDateString();
                    $data['user_id'] = $user_id;
                    $data['conversion_denominator'] = $conversion_denominator;
                    \App\Models\EfficencyData::create($data);
                }
            }
            $day->subDay();
            $bar->advance();
        }
        Settings::set('efficency_updated', now());
        Settings::set('efficency_updating', 0);
        $bar->finish();
    }

    private static function cloneQuery($query) {
        return clone $query;
    }
}
