<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    public static $states = [
        'new',
        'awaiting',
        'finished',
        'deny',
        'spam',
        'motivated_deny'
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
    public static function stateCounts()
    {
        $return = [];
        foreach (self::$states as $state) {
            $query = static::where('state', $state);
            $return[$state] = $query->count();
        }

        $return['all'] = array_sum($return);
        return $return;
    }

    /**
     * количество элементов пагинации в странице итогов.
     */
    public static function summaryItemsCount($filter = 'day')
    {
        $first_date = new \DateTime(\App\Models\Request::orderBy('created_at')->pluck('created_at')->first());

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
