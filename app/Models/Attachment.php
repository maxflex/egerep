<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Events\DebtRecalc;
use DB;

class Attachment extends Model
{
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
        'hide',
        'checked'
    ];
    protected $casts = [
        'grade' => 'int',
    ];
    protected $appends = ['user_login', 'account_data_count'];
    protected $with = ['archive', 'review'];
    protected static $commaSeparated = ['subjects'];
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
    public function scopeHasLessons($query)
    {
        return $query->whereRaw('(SELECT COUNT(*) FROM account_datas ad WHERE ad.tutor_id = attachments.tutor_id AND ad.client_id = attachments.client_id) > 0');
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

        static::created(function ($model) {
            event(new DebtRecalc($model->tutor_id));
        });
        static::deleted(function ($model) {
            \App\Models\AccountData::where('tutor_id', $model->tutor_id)->where('client_id', $model->client_id)->delete();
            event(new DebtRecalc($model->tutor_id));
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
		foreach(['', 'new', 'inprogress', 'ended'] as $state) {
			$new_search = clone $search;
			$new_search->state = $state;
			$counts['state'][$state] = static::search($new_search)->count();
		}
		foreach(['', 0, 1] as $checked) {
			$new_search = clone $search;
			$new_search->checked = $checked;
			$counts['checked'][$checked] = static::search($new_search)->count();
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
        return $counts;
    }

    public static function search($search)
    {
        $search = filterParams($search);

        /**
         * сделал с join чтобы сортировать
         */
        $query = static::with(['tutor', 'client']);

        if (isset($search->state)) {
            $query->searchByState($search->state);
        }

        $query->join('request_lists as r', 'request_list_id', '=', 'r.id');             /* request_id нужен чтобы генерить правильную ссылку для редактирования */
        $query->leftJoin('archives as a', 'a.attachment_id', '=', 'attachments.id');

        if (isset($search->checked)) {
           $query->where('attachments.checked', $search->checked);
        }

        $query->select(
            'attachments.*', 'r.request_id',
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

        return $query->orderBy('attachments.date', 'desc');
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
}
