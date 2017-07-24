<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use DB;

class Log extends Model
{
    public $loggable   = false;
    public $timestamps = false;

    // не включать эти таблицы в список полей
    const EXCEPT_TABLES = ['logs', 'distances', 'graph_distances', 'migrations', 'phone_duplicates', 'stations'];

    protected $appends = ['user'];

    /**
     * Кастомный лог
     */
    public static function custom($type, $user_id, $data = [])
    {
        DB::table('logs')->insert([
            'table'     => null,
            'user_id'   => $user_id,
            'data'      => json_encode($data),
            'type'      => $type,
            'ip'        => @$_SERVER['HTTP_X_REAL_IP'],
        ]);
    }

    public function getUserAttribute()
    {
        return \App\Models\User::find($this->user_id);
    }

    /**
     *
     */
    public static function getTables()
    {
        $tables = static::groupBy('table')->orderBy('table', 'asc')->pluck('table')->all();

        $return = [];

        foreach(array_diff($tables, static::EXCEPT_TABLES) as $table) {
            if ($table) {
                $return[$table] = \Schema::getColumnListing($table);
            }
        }

        return $return;
    }

    // public function getDataAttribute()
    // {
    //     return json_decode($this->attributes['data']);
    // }

    public static function counts($search)
    {
		foreach(array_merge(['', 0], User::real()->pluck('id')->all()) as $user_id) {
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

    public static function search($search, $order = 'desc')
    {
        $search = filterParams($search);
        $query = Log::query();

        if (isset($search->user_id)) {
            $query->where('user_id', $search->user_id);
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

        if (isset($search->row_id)) {
            $query->where('row_id', $search->row_id);
        }

        return $query->orderBy('created_at', $order);
    }
}
