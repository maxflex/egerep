<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EfficencyData extends Model
{
    protected $table = 'efficency_data';
    public $timestamps = false;

    protected $fillable = [
        'date',
        'user_id',
        'requests_new',
        'requests_awaiting',
        'requests_finished',
        'requests_deny',
        'requests_reasoned_deny',
        'requests_checked_reasoned_deny',
        'requests_total',
        'attachments_newest',
        'attachments_active',
        'attachments_archived_no_lessons',
        'attachments_archived_one_lesson',
        'attachments_archived_two_lessons',
        'attachments_archived_three_or_more_lessons',
        'attachments_total',
        'forecast',
        'conversion_denominator',
        'commission',
    ];

    protected $dates = ['date'];
}
