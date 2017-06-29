<?php

namespace App\Models;

use Cache;

use Illuminate\Database\Eloquent\Model;
use App\Events\IncomingRequest;
use App\Events\RequestUserChanged;
use DB;

class Request extends Model
{
    public static $states = [
        'невыполненные'                     => 'new',
        'в ожидании'                        => 'awaiting',
        'выполненные'                       => 'finished',
        'отказ по вине компании'            => 'deny',
        'обоснованный отказ'                => 'reasoned_deny',
        'подтвержденный обоснованный отказ' => 'checked_reasoned_deny'
    ];

    protected $attributes = [
        'state' => 'new',
    ];
    protected $with = ['lists'];
    protected $fillable = [
        'comment',
        'state',
        'client_id',
        'user_id',
        'user_id_created',
        'lists',
        'deny_reason',
        'google_id'
    ];

    protected $appends = ['number'];

    // ------------------------------------------------------------------------

    public function getNumberAttribute()
    {
        if ($this->id) {
            return DB::table('requests')->where('client_id', $this->client_id)->where('id', '<=', $this->id)->count();
        }
        return null;
    }

    public function getCommentAttribute()
    {
        return preg_replace('#\n+#', "\n", $this->attributes['comment']);
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function userCreated()
    {
        return $this->belongsTo('App\Models\User', 'user_id_created');
    }

    public function lists()
    {
        return $this->hasMany('App\Models\RequestList');
    }

    // ------------------------------------------------------------------------

    public function setListsAttribute($value)
    {
        foreach ($value as $list) {
            RequestList::find($list['id'])->update($list);
        }
    }

    // ------------------------------------------------------------------------

    protected static function boot()
    {
        static::deleted(function($request) {
            Client::removeWithoutRequests();
        });

        static::saving(function ($model) {
            if (! $model->exists) {
                $model->user_id_created = userIdOrSystem();
            } else {
                if ($model->changed('state') && $model->state == 'new') {
                    event(new IncomingRequest($model->id));
                }
                if ($model->getOriginal('state') == 'new' && $model->state != 'new') {
                    event(new IncomingRequest($model->id, true));
                }
                if ($model->changed('user_id')) {
                    event(new RequestUserChanged($model->id, $model->user_id));
                }
            }
        });
        static::saved(function($model) {
            DB::table('requests')->where('id', $model->id)->update(['errors' => \App\Models\Helpers\Request::errors($model)]);
        });
        static::created(function($model) {
            if ($model->state == 'new') {
                event(new IncomingRequest($model->id));
            }
        });
        static::deleted(function($model) {
            event(new IncomingRequest($model->id, true));
        });
    }

    /**
     * Search by status.
     */
    public function scopeSearchByState($query, $state = 'new')
    {
        /**
         *  @notice     в списке статусов нет all.
         *              in_array для all нужен.
         */
        if (isset($state)) {
            if (in_array($state, self::$states)) {
                return $query->where('state', $state);
            }
            if ($state == 'all_denies') {
                return $query->whereIn('state', ['deny', 'reasoned_deny', 'checked_reasoned_deny']);
            }
        }
    }

    /**
     * State counts
     */
    public static function stateCounts($user_id)
    {
        $return = [];
        foreach (self::$states as $state) {
            $query = static::where('state', $state);
            if (! empty($user_id)) {
                $query->where('user_id', $user_id);
            }
            $return[$state] = $query->count();
        }

        $return['all_denies'] = $return['deny'] + $return['reasoned_deny'] + $return['checked_reasoned_deny'];
        $return['all'] = array_sum($return);
        return $return;
    }

    /**
     * Error counts
     */
    public static function errorCounts($user_id)
    {
        $return = [];
        $query = static::query();
        if ($user_id) {
            $query->where('user_id', $user_id);
        }
        foreach(range(1, 22) as $error) {
            $return[$error] = cloneQuery($query)->whereRaw("FIND_IN_SET({$error}, errors)")->count();
        }
        $return[''] = $query->count();
        return $return;
    }

    /**
     * State counts
     */
    public static function userCounts($state)
    {
        $user_ids = Cache::remember('user_ids', 60, function() {
            return static::groupBy('user_id')->pluck('user_id')->all();
        });

        $return = [];
        foreach ($user_ids as $user_id) {
            $query = static::where('user_id', $user_id);
            if ((! empty($state) || strlen($state) > 0) && $state != 'all') {
                $query->where('state', $state);
            }
            $return[$user_id] = $query->count();
        }
        return $return;
    }

    /**
     * Search by user id
     */
    public function scopeSearchByUser($query, $user_id)
    {
        if (isset($user_id) && $user_id !== null) {
            return $query->where('user_id', $user_id);
        }
    }

    /**
     * Search by user id
     */
    public function scopeSearchByError($query, $error)
    {
        if (isset($error) && $error !== null) {
            return $query->whereRaw("FIND_IN_SET({$error}, errors)");
        }
    }

    /**
     * количество элементов пагинации в странице итогов.
     */
    public static function summaryItemsCount($filter = 'day')
    {
        $first_date = new \DateTime(static::orderBy('created_at')->pluck('created_at')->first());

        switch ($filter) {
            case 'day':
                return $first_date->diff(new \DateTime)->format('%a');
            case 'week':
                return intval($first_date->diff(new \DateTime)->format('%a') / 7);
            case 'month':
                return ((new \DateTime)->format('Y') -  $first_date->format('Y'))*12 + $first_date->diff(new \DateTime)->format('%m') + 1;
            case 'year':
                $cnt = (new \DateTime)->format('Y') - $first_date->format('Y');
                $cnt += (new \DateTime)->format('m') < 7
                        ? $first_date->format('m') < 7
                            ? 1
                            : 0
                        : $first_date->format('m') < 7
                          ? 2
                          : 1;
                return $cnt;
        }
    }
}
