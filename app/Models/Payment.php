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
        static::saving(function($model) {
            if (! $model->source_id) {
                $model->source_id = null;
            }
            if (! $model->addressee_id) {
                $model->addressee_id = null;
            }
            if (! $model->expenditure_id) {
                $model->expenditure_id = null;
            }
        });
        static::creating(function ($model) {
            $model->user_id = User::fromSession()->id;
        });
    }
}
