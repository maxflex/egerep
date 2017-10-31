<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;

class Expenditure extends Model
{
    protected $table = 'payment_expenditures';
    public $timestamps = false;

    protected $fillable = ['name', 'position', 'group_id'];
}
