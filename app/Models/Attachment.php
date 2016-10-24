<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Events\DebtRecalc;
use App\Events\AttachmentCountChanged;
use DB;
use Storage;

class Attachment extends Model
{
    const P_COEF = 0.72; // писсимизирующий коэффициент

    public static $states = ['new', 'inprogress', 'ended'];

    protected $fillable = [
        'request_list_id',
        'user_id',
        'tutor_id',
        'client_id',
        'date',
        'grade',
        'subjects',
        'archive',
        'review',
        'comment',
        'forecast',
        'hide'
    ];
    protected $casts = [
        'grade' => 'int',
    ];
    protected $appends = ['user_login', 'account_data_count'];
    protected $with = ['archive', 'review'];
    protected static $commaSeparated = ['subjects', 'errors'];
    protected static $dotDates = ['date'];

    // ------------------------------------------------------------------------

    public function requestList()
    {
        return $this->belongsTo('App\Models\RequestList');
    }

    public function archive()
    {
        return $this->hasOne('App\Models\Archive');
    }

    public function review()
    {
        return $this->hasOne('App\Models\Review');
    }

    public function tutor()
    {
        return $this->belongsTo('App\Models\Tutor');
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function accounts()
    {
        return $this->hasMany('App\Models\Account', 'tutor_id', 'tutor_id');
    }

    public function notifications()
    {
        return $this->morphMany('App\Models\Notification', 'entity');
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

    public function setArchiveAttribute($archive)
    {
        if ($archive !== null) {
            Archive::find($archive['id'])->update($archive);
        }
    }

    public function setReviewAttribute($review)
    {
        if ($review !== null) {
            Review::find($review['id'])->update($review);
        }
    }

    public function getAccountDataCountAttribute()
    {
        return AccountData::where('tutor_id',  $this->tutor_id)
                          ->where('client_id', $this->client_id)
                          ->count();
    }

    public function getLastAccountDateAttribute()
    {
        $account_data = AccountData::where('tutor_id',  $this->tutor_id)
                          ->where('client_id', $this->client_id)
                          ->orderBy('date','desc')
                          ->first();

        if ($account_data) {
            return $account_data->date;
        }

        return false;
    }

    /**
     * Дата последней встречи
     */
    public function getLastMeetingDateAttribute()
    {
        return Account::where('tutor_id', $this->tutor_id)->orderBy('date_end', 'desc')->value('date_end');
    }

    public function getFirstAccountDateAttribute()
    {
        $account_data = AccountData::where('tutor_id',  $this->tutor_id)
                            ->where('client_id', $this->client_id)
                            ->orderBy('date')
                            ->first();

        if ($account_data) {
            return $account_data->date;
        }

        return false;
    }

    public function getLinkAttribute()
    {
        return "requests/{$this->requestList->request->id}/edit#{$this->requestList->id}#{$this->id}";
    }

    // ------------------------------------------------------------------------

    /**
     * Search by status.
     */
    public function scopeSearchByState($query, $state = 'new')
    {
        if (isset($state) && in_array($state, self::$states)) {
            if ($state == 'ended') {
                return $query->archived();
            } else {
                if ($state == 'inprogress') {
                    return $query->active();
                } else {
                    return $query->newest();
                }
            }
        }
    }

    /**
     * Заархивированные стыковки
     */
    public function scopeArchived($query)
    {
        return $query->has('archive');
    }

    /**
     * Новые или в процессе стыковки
     */
    public function scopeNewOrActive($query)
    {
        return $query->doesntHave('archive');
    }

    /**
     * Заархивированные или рабочие
     */
    public function scopeArchivedOrActive($query)
    {
        return $query->whereRaw("(EXISTS (SELECT 1 FROM archives WHERE archives.attachment_id = attachments.id) OR (forecast > 0))");
    }

    /**
     * Рабочие стыковки
     */
    public function scopeActive($query)
    {
        return $query->doesntHave('archive')->where('forecast', '>', 0);
    }

    /**
     * Новые стыковки
     */
    public function scopeNewest($query)
    {
        return $query->doesntHave('archive')->whereNullOrZero('forecast');
    }

    /**
     * Стыковка без занятий
     */
    public function scopeNoLessons($query)
    {
        return $query->whereRaw('(SELECT COUNT(*) FROM account_datas ad WHERE ad.tutor_id = attachments.tutor_id AND ad.client_id = attachments.client_id) = 0');
    }

    /**
     * Стыковка с занятиями
     */
    public function scopeHasLessons($query, $lesson_count = '> 0')
    {
        return $query->whereRaw('(SELECT COUNT(*) FROM account_datas ad WHERE ad.tutor_id = attachments.tutor_id AND ad.client_id = attachments.client_id) ' . $lesson_count);
    }

    /**
     * Стыковка с занятиями + занятиями к проводке
     */
    public function scopeHasLessonsWithMissing($query, $lesson_count = '> 0')
    {
        return $query->whereRaw('((SELECT COUNT(*) FROM account_datas ad WHERE ad.tutor_id = attachments.tutor_id AND ad.client_id = attachments.client_id) + (SELECT total_commission FROM archives WHERE archives.attachment_id = attachments.id)) ' . $lesson_count);
    }

    /**
     * Получить статус стыковки
     */
    public function getState()
    {
        if ($this->archive()->exists()) {
            return 'ended';
        } else {
            return ($this->forecast ? 'inprogress' : 'new');
        }
    }

    /**
     * @return array    attachment counts by state.
     */
    public static function stateCounts()
    {
        $counts = [];
        foreach (Attachment::$states as $state) {
            $counts[$state] = Attachment::searchByState($state)->count();
        }
        $counts['all'] = array_sum($counts);

        return $counts;
    }

    // ------------------------------------------------------------------------

    protected static function boot()
    {
        static::saving(function ($model) {
            if (! $model->exists) {
                $model->date    = date('Y-m-d');
                $model->user_id = User::fromSession()->id;
            }
        });
        static::saved(function($model) {
            DB::table('attachments')->where('id', $model->id)->update(['errors' => \App\Models\Helpers\Attachment::errors($model)]);
        });
        static::created(function ($model) {
            event(new DebtRecalc($model->tutor_id));
            event(new AttachmentCountChanged());
        });
        static::deleted(function ($model) {
            AccountData::where('tutor_id', $model->tutor_id)->where('client_id', $model->client_id)->delete();
            event(new DebtRecalc($model->tutor_id));
            event(new AttachmentCountChanged(true));
        });
    }

    public function save(array $options = [])
    {
        $fire_event = $this->exists && $this->changed(['date', 'forecast']);

        parent::save($options);

        if ($fire_event) {
            event(new DebtRecalc($this->tutor_id));
        }
    }

    //
    //
    //

    public static function counts($search)
    {
        foreach(array_merge(['', 0], User::active()->pluck('id')->all()) as $user_id) {
			$new_search = clone $search;
			$new_search->user_id = $user_id;
			$counts['user'][$user_id] = static::search($new_search)->count();
		}
		foreach(['', 'new', 'inprogress', 'ended'] as $state) {
			$new_search = clone $search;
			$new_search->state = $state;
			$counts['state'][$state] = static::search($new_search)->count();
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
        return $counts;
    }

    public static function search($search)
    {
        $query = static::commonSearch($search);
        $query->leftJoin('archives as a', 'a.attachment_id', '=', 'attachments.id');
        $query->addSelect(
            'a.created_at AS archive_date', 'a.total_lessons_missing',
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
            $query->where('attachments.user_id', $search->user_id);
        }

        if (isset($search->debtor)) {
            $query->whereHas('tutor', function($query) use ($search) {
                $query->where('debtor', ($search->debtor ? '>' : '='), 0);
            });
        }

        if (isset($search->total_lessons_missing)) {
            if ($search->total_lessons_missing) {
                $query->whereNullOrZero('a.total_lessons_missing');
            } else {
                $query->where('a.total_lessons_missing', '>', 0);
            }
        }

        if (isset($search->error)) {
            $query->whereRaw("FIND_IN_SET({$search->error}, attachments.errors)");
        }

        return $query->orderBy('attachments.created_at', 'desc');
    }

    public static function commonSearch(&$search)
    {
        $search = filterParams($search);

        /**
         * сделал с join чтобы сортировать
         */
        $query = static::with(['tutor']);

        if (isset($search->state)) {
            $query->searchByState($search->state);
        }

        $query->join('request_lists as r', 'request_list_id', '=', 'r.id');/* request_id нужен чтобы генерить правильную ссылку для редактирования */
        $query->select('attachments.*', 'r.request_id');

        if (isset($search->state)) {
            $query->searchByState($search->state);
        }

        return $query;
    }

    public static function notificationCounts($search)
    {
        foreach(array_merge(['', 0], User::active()->pluck('id')->all()) as $user_id) {
            $new_search = clone $search;
            $new_search->user_id = $user_id;
            $counts['user_id'][$user_id] = static::notificationSearch($new_search)->count();
        }
        foreach(['', 'new', 'inprogress', 'ended'] as $state) {
            $new_search = clone $search;
            $new_search->state = $state;
            $counts['state'][$state] = static::notificationSearch($new_search)->count();
        }
        foreach(['', 0, 1] as $approved) {
            $new_search = clone $search;
            $new_search->approved = $approved;
            $counts['approved'][$approved] = static::notificationSearch($new_search)->count();
        }
        foreach(['', 0, 1] as $type) {
            $new_search = clone $search;
            $new_search->type = $type;
            $counts['type'][$type] = static::notificationSearch($new_search)->count();
        }
        foreach(['', 0, 1] as $created) {
            $new_search = clone $search;
            $new_search->created = $created;
            $counts['created'][$created] = static::notificationSearch($new_search)->count();
        }
        return $counts;
    }

    public static function notificationSearch($search)
    {
        $query = static::commonSearch($search);
        $query->leftJoin('notifications as n', function($join) use ($query) {
            $join->on('n.entity_id', '=', 'attachments.id')
                 ->where('n.entity_type', '=','attachment');
            $query->addSelect(
                'attachments.date AS original_date',
                'n.id AS notification_id',
                'n.user_id AS notification_user_id',
                'n.date as notification_date',
                'n.approved as notification_approved',
                'n.comment as notification_comment',
                'n.created_at as notification_created_at'
            );
        });

        if (isset($search->approved)) {
            if ($search->approved) {
                $query->where('n.approved', 1);
            } else {
                $query->where(function($query) {
                    $query->whereNull('n.id')->orWhere('n.approved', 0);
                });
            }
        }

        if (isset($search->type)) {
            // требующие звонка
            if ($search->type) {
                $query->whereRaw("((n.id IS NULL AND ((EXISTS (SELECT 1 FROM archives WHERE archives.attachment_id = attachments.id) OR (attachments.forecast > 0)) OR DATE_ADD(attachments.date, INTERVAL 2 DAY) > DATE(NOW()))) OR (n.id > 0 AND (n.approved = 1 OR n.date > DATE(NOW()))))");
            } else {
                $query->newest()->whereRaw("((n.id IS NULL AND DATE_ADD(attachments.date, INTERVAL 2 DAY) <= DATE(NOW())) OR (n.id > 0 AND n.approved = 0 AND n.date <= DATE(NOW())))");
            }
        }

        if (isset($search->created)) {
            if ($search->created) {
                $query->whereNotNull('n.id');
            } else {
                $query->whereNull('n.id');
            }
        }

        if (isset($search->user_id)) {
            $query->where('attachments.user_id', $search->user_id);
        }

        return $query->orderBy(DB::raw('IF(n.id IS NULL, DATE(DATE_ADD(attachments.date, INTERVAL 2 DAY)), n.date)'), 'desc')
                     ->orderBy('attachments.id', 'desc');
    }

    /**
     * Получить дату последнего занятия по ID стыковки
     */
    public static function getLastLessonDate($attachment_id)
    {
        return DB::table('attachments')->join(DB::raw('(
              SELECT MAX(date) as last_lesson_date, tutor_id, client_id
              FROM account_datas
              GROUP BY tutor_id, client_id
          ) ad'), function($join) {
            $join->on('attachments.tutor_id', '=', 'ad.tutor_id')
                 ->on('attachments.client_id', '=', 'ad.client_id');
            })->where('id', $attachment_id)->value('last_lesson_date');
    }

    /**
     * Получить по месяцам для статистики
     */
    public static function getStatsByMonth($month)
    {
        $current_year = date('Y');
        $year_from = $current_year - 4;

        return DB::table('attachments')
            ->select(DB::raw('user_id, DAY(created_at) as day, YEAR(created_at) as year, COUNT(*) as count'))
            ->whereRaw("(YEAR(created_at) <= $current_year AND YEAR(created_at) >= $year_from) AND MONTH(created_at) = $month")
            ->groupBy(DB::raw('user_id, DAY(created_at), YEAR(created_at)'))
            ->get();
    }

    /**
     * Кол-во стыковок сегодня
     */
    public static function countToday()
    {
        return static::whereRaw('DATE(NOW()) = DATE(created_at)')->count();
    }
}
