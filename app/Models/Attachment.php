<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attachment extends Model
{
    public static $states = ['new', 'inprogress', 'ended'];

    protected $fillable = [
        'request_list_id',
        'user_id',
        'tutor_id',
        'client_id',
        'date',
        'grade',
        'subjects',
        'archive',
        'review',
        'comment',
        'forecast',
        'hide'
    ];
    protected $casts = [
        'grade' => 'int',
    ];
    protected $appends = ['user_login', 'account_data_count'];
    protected $with = ['archive', 'review'];
    protected static $commaSeparated = ['subjects'];
    protected static $dotDates = ['date'];

    // ------------------------------------------------------------------------

    public function requestList()
    {
        return $this->belongsTo('App\Models\RequestList');
    }

    public function archive()
    {
        return $this->hasOne('App\Models\Archive');
    }

    public function review()
    {
        return $this->hasOne('App\Models\Review');
    }

    public function tutor()
    {
        return $this->belongsTo('App\Models\Tutor');
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
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

    public function setArchiveAttribute($archive)
    {
        if ($archive !== null) {
            Archive::find($archive['id'])->update($archive);
        }
    }

    public function setReviewAttribute($review)
    {
        if ($review !== null) {
            Review::find($review['id'])->update($review);
        }
    }

    public function getAccountDataCountAttribute()
    {
        return AccountData::where('tutor_id',  $this->tutor_id)
                          ->where('client_id', $this->client_id)
                          ->count();
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
    }

    /**
     * Search by status.
     */
    public function scopeSearchByState($query, $state = 'new')
    {
        if (isset($state) && in_array($state, self::$states)) {
            if ($state == 'ended') {
                return $query->has('archive');
            } else {
                $query->has('archive', '=', 0);
                if ($state == 'inprogress') {
                    return $query->where('forecast', '>', 0);
                } else {
                    return $query->whereNull('forecast');
                }
            }
        }
    }

    /**
     * Получить статус стыковки
     */
    public function getState()
    {
        if ($this->archive()->exists()) {
            return 'ended';
        } else {
            return ($this->forecast ? 'inprogress' : 'new');
        }
    }

    /**
     * @return array    attachment counts by state.
     */
    public static function stateCounts()
    {
        $counts = [];
        foreach (Attachment::$states as $state) {
            $counts[$state] = Attachment::searchByState($state)->count();
        }
        $counts['all'] = array_sum($counts);
        
        return $counts;
    }
}
