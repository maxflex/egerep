<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;

class Addressee extends Model
{
    protected $table = 'payment_addressees';
    public $timestamps = false;

    protected $fillable = ['name'];
}
