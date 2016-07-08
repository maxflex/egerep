<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;

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

    public static function search($search)
    {
        $search = filterParams($search);
        $query = Log::query();

        // if (isset($search->mode)) {
        //     if ($search->mode) {
        //         $query->doesntHave('review');
        //     } else {
        //         $query->has('review');
        //     }
        // }
        //
        // if (isset($search->state) || isset($search->signature) || isset($search->comment) || isset($search->score)) {
        //     $query->whereHas('review', function($query) use ($search) {
        //         if (isset($search->state)) {
        //             $query->where('state', $search->state);
        //         }
        //         if (isset($search->signature)) {
        //             $query->where('signature', $search->signature ? '=' : '<>', '');
        //         }
        //         if (isset($search->comment)) {
        //             $query->where('comment', $search->comment ? '=' : '<>', '');
        //         }
        //         if (isset($search->score)) {
        //             $query->where('score', $search->score);
        //         }
        //     });
        // }

        return $query->orderBy('created_at', 'desc');
    }
}
