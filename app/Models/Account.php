<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AccountData;

class Account extends Model
{
    protected $fillable = [
        'date_end',
        'tutor_id',
        'total_commission',
        'received',
        'debt',
        'debt_type',
        'debt_before',
        'comment',
        'payment_method',
        'data',
    ];

    protected $appends = ['data'];

    // ------------------------------------------------------------------------

    public function tutor()
    {
        return $this->belongsTo('App\Models\Tutor');
    }

    public function accountData()
    {
        return $this->hasMany('App\Models\AccountData', 'tutor_id', 'tutor_id');
    }

    // ------------------------------------------------------------------------

    public function getDataAttribute($value)
    {
        foreach ($this->accountData as $d) {
            $return[$d->client_id][$d->date] = $d->value;
        }
        return $return;
    }

    public function setDataAttribute($value)
    {
        foreach ($value as $client_id => $data) {
            foreach ($data as $date => $value) {
                AccountData::updateOrCreate([
                    'client_id' => $client_id,
                    'tutor_id'  => $this->tutor_id,
                    'date'      => $date,
                ], ['value' => $value]);
            }
        }
    }
}
