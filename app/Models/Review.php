<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Attachment;

class Review extends Model
{
    protected $fillable = [
        'attachment_id',
        'score',
        'comment',
        'state',
        'signature',
    ];
    protected $appends = ['user_login'];

    // ------------------------------------------------------------------------

    public function getUserLoginAttribute()
    {
        if (! $this->user_id) {
            return 'system';
        } else {
            return User::where('id', $this->user_id)->pluck('login')->first();
        }
    }

    // ------------------------------------------------------------------------

    protected static function boot()
    {
        static::saving(function ($model) {
            if (! $model->exists) {
                $model->user_id = User::fromSession()->id;
            }
        });
    }

    // ------------------------------------------------------------------------

    public static function counts($search)
    {
		foreach(["", 0, 1] as $mode) {
			$new_search = clone $search;
			$new_search->mode = $mode;
			$counts['mode'][$mode] = static::search($new_search)->count();
		}
		foreach(["", "published", "unpublished"] as $state) {
			$new_search = clone $search;
			$new_search->state = $state;
			$counts['state'][$state] = static::search($new_search)->count();
		}
		foreach(["", 0, 1] as $signature) {
			$new_search = clone $search;
			$new_search->signature = $signature;
			$counts['signature'][$signature] = static::search($new_search)->count();
		}
		foreach(["", 0, 1] as $comment) {
			$new_search = clone $search;
			$new_search->comment = $comment;
			$counts['comment'][$comment] = static::search($new_search)->count();
		}
        foreach(array_merge([""], range(1, 12)) as $score) {
            $new_search = clone $search;
            $new_search->score = $score;
            $counts['score'][$score] = static::search($new_search)->count();
        }
        return $counts;
    }

    public static function search($search)
    {
        $search = filterParams($search);
        $query = Attachment::with(['tutor'])->has('archive');

        if (isset($search->mode)) {
            if ($search->mode) {
                $query->doesntHave('review');
            } else {
                $query->has('review');
            }
        }

        if (isset($search->state) || isset($search->signature) || isset($search->comment) || isset($search->score)) {
            $query->whereHas('review', function($query) use ($search) {
                if (isset($search->state)) {
                    $query->where('state', $search->state);
                }
                if (isset($search->signature)) {
                    $query->where('signature', $search->signature ? '=' : '<>', '');
                }
                if (isset($search->comment)) {
                    $query->where('comment', $search->comment ? '=' : '<>', '');
                }
                if (isset($search->score)) {
                    $query->where('score', $search->score);
                }
            });
        }

        return $query->orderBy('date', 'desc');
    }
}
