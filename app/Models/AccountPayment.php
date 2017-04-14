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
    ];
    protected static $dotDates = ['date'];
}
