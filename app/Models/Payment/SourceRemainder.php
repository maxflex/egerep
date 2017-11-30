<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;

class SourceRemainder extends Model
{
    protected $table = 'payment_source_remainders';
    public $timestamps = false;
    protected static $dotDates = ['date'];
    protected $appends = ['remainder_comma'];
    protected $fillable = ['remainder', 'remainder_comma', 'date', 'source_id'];

    public function getRemainderCommaAttribute()
    {
        return str_replace('.', ',', $this->remainder);
    }

    public function setRemainderCommaAttribute($value)
    {
        $this->attributes['remainder'] = str_replace(',', '.', $value);
    }
}
