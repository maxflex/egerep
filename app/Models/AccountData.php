<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountData extends Model
{
    protected $fillable = [
        'tutor_id',
        'client_id',
        'date',
        'value',
    ];
    public $timestamps = false;

    // ------------------------------------------------------------------------

    public function setValueAttribute($value)
    {
        $data = explode('/', $value);
        $this->sum = $data[0];
        $this->commission = $data[1];
    }

    public function getValueAttribute()
    {
        return $this->sum . '/' . $this->commission;
    }

}
