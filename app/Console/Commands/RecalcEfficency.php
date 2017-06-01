<?php

namespace App\Console\Commands;

use App\Jobs\RecalcUserEfficency;
use Illuminate\Console\Command;
use Carbon\Carbon;

class RecalcEfficency extends Command
{
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
        $user_ids = array_merge([0], \App\Models\User::real()->get()->pluck('id')->all());
        $bar = $this->output->createProgressBar(count($user_ids));
        Settings::set('efficency_updating', 1);
//        foreach([1459, 1367] as $user_id) {
        foreach($user_ids as $user_id) {
            $today = Carbon::today();
            $calcing_date = Carbon::today()->subYear();
            while ($calcing_date < $today) {
                $data = [
                    'date'    => $calcing_date->toDateString(),
                    'user_id' => $user_id
                ];

                $request_query = \App\Models\Request::query();
                $attachments_query = \App\Models\Attachment::query();

                $request_query->whereDate('requests.created_at', '=', $calcing_date);
                $attachments_query->where('attachments.date', $calcing_date);


                $request_query->where('requests.user_id', $user_id);
                $attachments_query->where('attachments.user_id', $user_id);

                foreach (\App\Models\Request::$states as $request_state) {
                    $data['requests_' . $request_state] = static::cloneQuery($request_query)->searchByState($request_state)->count();
                }
                $data['requests_total'] = $request_query->count();

                $data['attachments_total'] = static::cloneQuery($attachments_query)->count();
                $data['attachments_newest'] = static::cloneQuery($attachments_query)->newest()->count();
                $data['attachments_active'] = static::cloneQuery($attachments_query)->active()->count();
                $data['attachments_archived_no_lessons'] = static::cloneQuery($attachments_query)->archived()->hasLessonsWithMissing('=0')->count();
                $data['attachments_archived_one_lesson'] = static::cloneQuery($attachments_query)->archived()->hasLessonsWithMissing('=1')->count();
                $data['attachments_archived_two_lessons'] = static::cloneQuery($attachments_query)->archived()->hasLessonsWithMissing('=2')->count();
                $data['attachments_archived_three_or_more_lessons'] = static::cloneQuery($attachments_query)->archived()->hasLessonsWithMissing('>=3')->count();

                $data['forecast'] = static::cloneQuery($attachments_query)->active()->sum('forecast') + static::cloneQuery($attachments_query)->archived()->hasLessonsWithMissing('>=3')->sum('forecast');

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

                $conversion_denominator = 0;
                foreach ($request_ids as $request_id) {
                    $numerator   = $request_attachments_count[$request_id]->attachments_count;
                    $denominator = $request_attachments_count_without_users[$request_id]->attachments_count;
                    $conversion_denominator += $numerator / $denominator;
                }
                $conversion_denominator += $data['requests_deny'];
                $data['conversion_denominator'] = $conversion_denominator;

                \App\Models\EfficencyData::create($data);
                $calcing_date->addDay();
            }
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
