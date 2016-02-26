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
        if (count($data) >= 1) {
            $this->sum = $data[0];
        }
        if (count($data) == 2) {
            $this->commission = $data[1];
        }
    }

    public function getValueAttribute()
    {
        return $this->sum . ($this->commission ? '/' . $this->commission : '');
    }

    /**
     * Не отображать ноль
     */
    public function getSumAttribute($value)
    {
        if (!$value) {
            $value = null;
        }
        return $value;
    }

    public function getComissionAttribute($value)
    {
        if (!$value) {
            $value = null;
        }
        return $value;
    }

}