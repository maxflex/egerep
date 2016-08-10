<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\IncomingRequest;

class Request extends Model
{
    public static $states = [
        'невыполненные'      => 'new',
        'в ожидании'         => 'awaiting',
        'выполненные'        => 'finished',
        'отказы'             => 'deny',
        'обоснованный отказ' => 'reasoned_deny'
    ];

    protected $attributes = [
        'state' => 'new',
    ];
    protected $with = ['user', 'lists'];
    protected $fillable = [
        'comment',
        'state',
        'client_id',
        'user_id',
        'user_id_created',
        'lists',
        'id_a_pers',
        'deny_reason',
        'google_id'
    ];

    // ------------------------------------------------------------------------

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
            }
        });
        // нужно удалить после решения @todo выше
        static::created(function($model) {
            if ($model->state == 'new') {
                event(new IncomingRequest($model->id));
            }
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
        if (isset($state) && in_array($state, self::$states)) {
            return $query->where('state', $state);
        }
    }

    /**
     * State counts
     * @return array [state_id] => state_count
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

        $return['all'] = array_sum($return);
        return $return;
    }

    /**
     * State counts
     * @return array [user_id] => state_count
     */
    public static function userCounts($state)
    {
        $user_ids = static::where('user_id', '>', 0)->groupBy('user_id')->pluck('user_id');
        $user_ids[] = 0;
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
