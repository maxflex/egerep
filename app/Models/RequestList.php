<?php

namespace App\Models;

use DB;
use App\Models\Tutor;
use App\Models\Marker;
use Illuminate\Database\Eloquent\Model;

class RequestList extends Model
{
    protected $with = ['attachments'];
//    protected $appends = [
//        'tutors'
//    ];
    protected $fillable = [
        'request_id',
        'tutor_ids',
        'subjects',
        'attachments',
        'user_id',
    ];
    protected static $commaSeparated = ['tutor_ids', 'subjects'];

    // ------------------------------------------------------------------------

    public function request()
    {
        return $this->belongsTo('App\Models\Request');
    }

    public function attachments()
    {
        return $this->hasMany('App\Models\Attachment');
    }

    // ------------------------------------------------------------------------

    public function setAttachmentsAttribute($value)
    {
        foreach ($value as $attachment) {
            Attachment::find($attachment['id'])->update($attachment);
        }
    }

    public function getTutorsAttribute()
    {
        $tutors = Tutor::with(['markers'])->whereIn('id', $this->tutor_ids)->get([
            'id',
            'first_name',
            'last_name',
            'middle_name',
            'birth_year',
            'subjects',
            'tb',
            'lk',
            'js',
            'photo_extension',
            'margin',
            'public_price',
            'departure_price',
            DB::raw("(select count(*) from tutor_departures td where td.tutor_id = tutors.id) as departure_possible")
        ])->append(['clients_count', 'meeting_count', 'active_clients_count', 'last_account_info', 'svg_map']);
        $client_marker_id = DB::table('request_lists')
            ->join('requests', 'request_lists.request_id', '=', 'requests.id')
            ->join('markers', function($join) {
                $join->on('markers.markerable_id', '=', 'requests.client_id')
                    ->where('markers.markerable_type', '=', 'App\Models\Client');
            })->where('request_lists.id', $this->id)->select('markers.id')->value('id');
        if ($client_marker_id) {
            $client_marker = Marker::find($client_marker_id);
            foreach ($tutors as &$tutor) {
                # Получить минуты
                $tutor->minutes = $tutor->getMinutes($client_marker);
            }
        }
        return $tutors;
    }

    // ------------------------------------------------------------------------

    protected static function boot()
    {
        static::saving(function ($model) {
            if (! $model->exists) {
                $model->user_id = User::id();
            } else {
                $model->tutor_ids = array_unique($model->tutor_ids);
            }
        });
    }
}
