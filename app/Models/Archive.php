<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\DebtRecalc;

class Archive extends Model
{
    protected $fillable = [
        'attachment_id',
        'total_lessons_missing',
        'date',
        'comment',
        'state',
    ];
    protected $appends = ['user_login'];
    protected static $dotDates = ['date'];

    // ------------------------------------------------------------------------

    public function attachment()
    {
        return $this->belongsTo('App\Models\Attachment');
    }

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
                $model->date = date('Y-m-d');
                $model->user_id = User::fromSession()->id;
            }
        });
        static::saved(function($model) {
            DB::table('attachments')->where('id', $model->attachment_id)->update(['errors' => \App\Models\Helpers\Attachment::errors($model->attachment)]);
        });
        static::created(function ($model) {
            event(new DebtRecalc($model->attachment->tutor_id));
        });
        static::deleted(function ($model) {
            event(new DebtRecalc($model->attachment->tutor_id));
        });
    }

    public function save(array $options = [])
    {
        $fire_event = $this->exists && $this->changed(['date']);

        parent::save($options);

        if ($fire_event) {
            event(new DebtRecalc($this->attachment->tutor_id));
        }
    }
}
