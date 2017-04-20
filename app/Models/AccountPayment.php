<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountPayment extends Model
{
    protected $fillable = [
        'account_id',
        'sum',
        'date',
        'method',
        'confirmed'
    ];
    protected static $dotDates = ['date'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    protected static function boot()
    {
        static::saving(function ($model) {
            if (! $model->exists) {
                $model->user_id = User::fromSession()->id;
            }
        });
    }
}
