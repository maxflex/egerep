<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'sum',
        'loan',
        'purpose',
        'date',
        'addressee_id',
        'source_id',
        'expenditure_id'
    ];

    protected $attributes = [
        'source_id'      => '',
        'addressee_id'   => '',
        'expenditure_id' => '',
    ];

    protected static $dotDates = ['date'];

    protected static function boot()
    {
        static::creating(function ($model) {
            $model->user_id = User::fromSession()->id;
        });
    }
}
