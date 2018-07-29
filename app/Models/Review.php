<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Attachment;
use App\Events\RecalcTutorData;

class Review extends Model
{
    protected $fillable = [
        'attachment_id',
        'score',
        'comment',
        'state',
        'ball',
        'max_ball',
        'signature',
    ];
    protected $appends = ['user_login'];
    protected static $commaSeparated = ['errors'];

    // ------------------------------------------------------------------------

    public function attachment()
    {
        return $this->belongsTo(Attachment::class);
    }

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
                $model->user_id = User::id();
            }
        });
        static::saved(function($model) {
            \DB::table('reviews')->whereId($model->id)->update(['errors' => \App\Models\Helpers\Review::errors($model)]);
        });
        static::updated(function($model) {
            if ($model->changed(['state', 'score'])) {
                event(new RecalcTutorData($model->attachment->tutor_id));
            }
        });
        static::deleted(function ($model) {
            event(new RecalcTutorData($model->attachment->tutor_id));
        });
    }

    // ------------------------------------------------------------------------

    public static function counts($search)
    {
        foreach(array_merge(['', 0], User::pluck('id')->all()) as $user_id) {
			$new_search = clone $search;
			$new_search->user_id = $user_id;
			$counts['user'][$user_id] = static::search($new_search)->count();
		}
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
        foreach(array_merge([''], range(1, 6)) as $error) {
            $new_search = clone $search;
            $new_search->error = $error;
            $counts['error'][$error] = static::search($new_search)->count();
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

        if (isset($search->tutor_id)) {
            $query->where("tutor_id", $search->tutor_id);
        }

        if (isset($search->state) || isset($search->signature) || isset($search->comment) || isset($search->score) || isset($search->user_id) || isset($search->error)) {
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
                if (isset($search->user_id)) {
                    $query->where('user_id', $search->user_id);
                }
                if (isset($search->error)) {
                    $query->whereRaw("FIND_IN_SET({$search->error}, errors)");
                }
            });
        }

        return $query->orderBy('created_at', 'desc');
        // return $query->orderBy(\DB::raw("(SELECT CONCAT(last_name, first_name, middle_name) as name from tutors where tutors.id = attachments.tutor_id)"), 'asc');
    }
}
