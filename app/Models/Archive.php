<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\DebtRecalc;

class Archive extends Model
{
    protected $fillable = [
        'attachment_id',
        'total_lessons_missing',
        'date',
        'comment',
        'state',
        'checked',
    ];
    protected $appends = ['user_login'];
    protected static $dotDates = ['date'];

    // ------------------------------------------------------------------------

    public function attachment()
    {
        return $this->belongsTo('App\Models\Attachment');
    }

    public function tutor()
    {
        return $this->attachment->tutor();
    }

    // ------------------------------------------------------------------------

    public function getUserLoginAttribute()
    {
        if (! $this->user_id) {
            return 'system';
        } else {
            return User::where('id', $this->user_id)->pluck('login')->first();
        }
    }

    // ------------------------------------------------------------------------

    protected static function boot()
    {
        static::saving(function ($model) {
            if (!$model->exists) {
                $model->date = date('Y-m-d');
                $model->user_id = User::fromSession()->id;
            }
        });

        static::created(function ($model) {
            event(new DebtRecalc($model->attachment->tutor_id));
        });
        static::deleted(function ($model) {
            event(new DebtRecalc($model->attachment->tutor_id));
        });
    }

    public function save(array $options = [])
    {
        $fire_event = $this->exists && $this->changed(['date']);

        parent::save($options);

        if ($fire_event) {
            event(new DebtRecalc($this->attachment->tutor_id));
        }
    }

    /**
     * Кол-во стыковок сегодня
     */
    public static function countToday()
    {
        return static::whereRaw('DATE(NOW()) = DATE(created_at)')->count();
    }

    public static function counts($search)
    {
        foreach(array_merge(['', 0], User::active()->pluck('id')->all()) as $user_id) {
            $new_search = clone $search;
            $new_search->user_id = $user_id;
            $counts['user'][$user_id] = static::search($new_search)->count();
        }

        foreach(['', 0, 1] as $account_data) {
            $new_search = clone $search;
            $new_search->account_data = $account_data;
            $counts['account_data'][$account_data] = static::search($new_search)->count();
        }
        foreach(['', 0, 1] as $total_lessons_missing) {
            $new_search = clone $search;
            $new_search->total_lessons_missing = $total_lessons_missing;
            $counts['total_lessons_missing'][$total_lessons_missing] = static::search($new_search)->count();
        }
        foreach(['', 0, 1] as $forecast) {
            $new_search = clone $search;
            $new_search->forecast = $forecast;
            $counts['forecast'][$forecast] = static::search($new_search)->count();
        }
        foreach(['', 0, 1] as $debtor) {
            $new_search = clone $search;
            $new_search->debtor = $debtor;
            $counts['debtor'][$debtor] = static::search($new_search)->count();
        }
        foreach(['', 0, 1] as $hide) {
            $new_search = clone $search;
            $new_search->hide = $hide;
            $counts['hide'][$hide] = static::search($new_search)->count();
        }
        foreach(array_merge([''], range(1, 15)) as $error) {
            $new_search = clone $search;
            $new_search->error = $error;
            $counts['error'][$error] = static::search($new_search)->count();
        }
        foreach(array_merge([''], range(1, 13)) as $grade) {
            $new_search = clone $search;
            $new_search->grade = $grade;
            $counts['grade'][$grade] = static::search($new_search)->count();
        }
        foreach(['', 'impossible', 'possible'] as $state) {
            $new_search = clone $search;
            $new_search->state = $state;
            $counts['state'][$state] = static::search($new_search)->count();
        }
        foreach(['', 0, 1] as $checked) {
            $new_search = clone $search;
            $new_search->checked = $checked;
            $counts['checked'][$checked] = static::search($new_search)->count();
        }
        return $counts;
    }

    public static function search($search)
    {
        $search = filterParams($search);

        /**
         * сделал с join чтобы сортировать
         */
        $query = static::query()->with('tutor');


        $query->join('attachments', 'attachments.id', '=', 'archives.attachment_id');
        $query->join('request_lists as r', 'attachments.request_list_id', '=', 'r.id');             /* request_id нужен чтобы генерить правильную ссылку для редактирования */
        $query->join('clients as c', 'attachments.client_id', '=', 'c.id');

        $query->select(
            \DB::raw('archives.*, attachments.*, r.request_id, archives.created_at as archive_created_at, archives.date as archive_date, archives.user_id as archive_user_id, archives.id as archive_id, c.grade as client_grade'),
            'attachments.date AS attachment_date', 'total_lessons_missing',
            \DB::raw('(SELECT COUNT(*) FROM account_datas ad WHERE ad.tutor_id = attachments.tutor_id AND ad.client_id = attachments.client_id) as lesson_count')
        );

        if (isset($search->account_data)) {
            $query->whereRaw('(SELECT COUNT(*) FROM account_datas ad WHERE ad.tutor_id = attachments.tutor_id AND ad.client_id = attachments.client_id) ' . ($search->account_data ? '=' : '>') . 0);
        }

        if (isset($search->forecast)) {
            if ($search->forecast) {
                $query->whereNullOrZero('attachments.forecast');
            } else {
                $query->where('attachments.forecast', '>', 0);
            }
        }
        if (isset($search->hide)) {
            $query->where('attachments.hide', $search->hide);
        }
        if (isset($search->user_id)) {
            $query->where('archives.user_id', $search->user_id);
        }

        if (isset($search->debtor)) {
            $query->whereHas('tutor', function($query) use ($search) {
                $query->where('debtor', ($search->debtor ? '>' : '='), 0);
            });
        }

        if (isset($search->tutor_id)) {
            $query->where('attachments.tutor_id', $search->tutor_id);
        }

        if (isset($search->total_lessons_missing)) {
            if ($search->total_lessons_missing) {
                $query->whereNullOrZero('total_lessons_missing');
            } else {
                $query->where('total_lessons_missing', '>', 0);
            }
        }

        if (isset($search->error)) {
            $query->whereRaw("FIND_IN_SET({$search->error}, attachments.errors)");
        }

        if (isset($search->grade)) {
            $query->where('c.grade', $search->grade);
        }

        if (isset($search->state)) {
            $query->where('archives.state', $search->state);
        }

        if (isset($search->checked)) {
            $query->where('archives.checked', $search->checked);
        }

        return $query->orderBy('archives.created_at', 'desc');
    }
}
