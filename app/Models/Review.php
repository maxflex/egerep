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
            if (!$model->exists) {
                $model->user_id = User::fromSession()->id;
            }
        });
    }

    // ------------------------------------------------------------------------

    public function search($search)
    {
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

        return $query;
    }
}
