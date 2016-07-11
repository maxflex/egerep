<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Log extends Model
{
    public $loggable   = false;
    public $timestamps = false;

    protected $appends = ['user'];

    public function getUserAttribute()
    {
        return \App\Models\User::find($this->user_id);
    }

    // public function getDataAttribute()
    // {
    //     return json_decode($this->attributes['data']);
    // }

    public static function counts($search)
    {
		foreach(array_merge(['', 0], User::active()->pluck('id')->all()) as $user_id) {
			$new_search = clone $search;
			$new_search->user_id = $user_id;
			$counts['user'][$user_id] = static::search($new_search)->count();
		}
        foreach(['', 'update', 'create', 'delete'] as $type) {
			$new_search = clone $search;
			$new_search->type = $type;
			$counts['type'][$type] = static::search($new_search)->count();
		}
        return $counts;
    }

    public static function search($search)
    {
        $search = filterParams($search);
        $query = Log::query();

        if (isset($search->user_id)) {
            $query->where('user_id', $search->user_id);
        }

        if (isset($search->date_start)) {
            $query->where('created_at', '>=', fromDotDate($search->date_start));
        }

        if (isset($search->date_end)) {
            $query->where('created_at', '<=', fromDotDate($search->date_end));
        }

        if (isset($search->type)) {
            $query->where('type', $search->type);
        }

        return $query->orderBy('created_at', 'desc');
    }
}
