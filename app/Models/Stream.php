<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Stream extends Model
{
    public $table = 'stream';
    public $timestamps = false;
    public static $commaSeparated = ['subjects'];

    public function tutor()
    {
        return $this->belongsTo('App\Models\Tutor')->select(['id', 'first_name','last_name', 'middle_name']);
    }

    public static function counts($search)
    {
		foreach(['', 0, 1] as $mobile) {
			$new_search = clone $search;
			$new_search->mobile = $mobile;
			$counts['mobile'][$mobile] = static::search($new_search)->count();
		}
        foreach(self::groupBy('action')->pluck('action') as $action) {
			$new_search = clone $search;
			$new_search->action = $action;
			$counts['action'][$action] = static::search($new_search)->count();
		}
        foreach(self::groupBy('type')->pluck('type') as $type) {
			$new_search = clone $search;
			$new_search->type = $type;
			$counts['type'][$type] = static::search($new_search)->count();
		}
        return $counts;
    }

    public static function search($search)
    {
        $search = filterParams($search);
        $query = Stream::with('tutor')->orderBy('id', 'desc');

        if (isset($search->mobile)) {
            $query->where('mobile', $search->mobile);
        }

        if (isset($search->action)) {
            $query->where('action', $search->action);
        }

        if (isset($search->type)) {
            $query->where('type', $search->type);
        }

        if (isset($search->date_start)) {
            $query->where('created_at', '>=', fromDotDate($search->date_start) . ' 00:00:00');
        }

        if (isset($search->date_end)) {
            $query->where('created_at', '<=', fromDotDate($search->date_end) . ' 23:59:59');
        }

        return $query;
    }
}
