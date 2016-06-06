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
        if (strpos($value,'/') === false) {
            $value .= '/';
        }

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

    protected static function boot()
    {
        static::saving(function($model) {
            if ($model->sum == 0 && $model->commission == 0) {
                $model->delete();
                return false;
            }
        });
    }

}
