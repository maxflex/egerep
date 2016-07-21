<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Log extends Model
{
    public $loggable   = false;
    public $timestamps = false;

    // не включать эти таблицы в список полей
    const EXCEPT_TABLES = ['logs', 'distances', 'graph_distances', 'graph_places', 'migrations', 'phone_duplicates', 'stations'];

    protected $appends = ['user'];

    public function getUserAttribute()
    {
        return \App\Models\User::find($this->user_id);
    }

    /**
     *
     */
    public static function getTables()
    {
        return static::groupBy('table')->orderBy('table', 'asc')->pluck('table')->all();
    }

    public static function getColumns()
    {
        $columns = [];
        $tables = \DB::select('SHOW TABLES');
        foreach($tables as $t) {
            $table_name = $t->Tables_in_egerep;
            if (! in_array($table_name, static::EXCEPT_TABLES)) {
                foreach(\Schema::getColumnListing($table_name) as $column) {
                    $columns[] = $column;
                }
            }
        }
        sort($columns);
        return array_unique($columns);
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
        foreach(array_merge([''], static::getTables()) as $table) {
			$new_search = clone $search;
			$new_search->table = $table;
			$counts['table'][$table] = static::search($new_search)->count();
		}
        foreach(array_merge([''], static::getColumns()) as $column) {
			$new_search = clone $search;
			$new_search->column = $column;
			$counts['column'][$column] = static::search($new_search)->count();
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
            $query->where('created_at', '>=', fromDotDate($search->date_start) . ' 00:00:00');
        }

        if (isset($search->date_end)) {
            $query->where('created_at', '<=', fromDotDate($search->date_end) . ' 23:59:59');
        }

        if (isset($search->type)) {
            $query->where('type', $search->type);
        }

        if (isset($search->table)) {
            $query->where('table', $search->table);
        }

        if (isset($search->column)) {
            $query->where('data', 'like', "%{$search->column}%");
        }

        return $query->orderBy('created_at', 'desc');
    }
}
