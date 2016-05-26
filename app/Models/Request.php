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
        static::saving(function ($model) {
            if (!$model->exists) {
                $model->user_id_created = User::fromSession()->id;
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
}
