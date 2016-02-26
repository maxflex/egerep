<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AccountData;
use Carbon\Carbon;

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

    /**
     * Данные по отчетности
     */
    public function accountData()
    {
        return $this->hasMany('App\Models\AccountData', 'tutor_id', 'tutor_id')
            ->where('date', '>', $this->date_start)
            ->where('date', '<=', $this->date_end);
    }

    // ------------------------------------------------------------------------

    public function getDataAttribute()
    {
        // обязательно возвращать пустой объект, если данные пусты,
        // иначе на фронт-энде вернется пустой массив и будут проблемы
        if (! count($this->accountData)) {
            return emptyObject();
        }

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

    /**
     * Получить дату начала встречи (= конец предыдущей встречи или дата самой ранней стыковки)
     */
    public function getDateStartAttribute()
    {
        $date_start = static::where('date_end', '<', $this->date_end)->where('tutor_id', $this->tutor_id)->pluck('date_end')->first();

        // если предыдущей встречи нет, то дата самой ранней стыковки
        if ($date_start === null) {
            // к дате нужно прибавить -1, чтобы входило в $this->data
            $date_start = Carbon::createFromFormat('Y-m-d', $this->tutor->getFirstAttachmentDate())->subDay();
        }

        return $date_start;
    }

    // ------------------------------------------------------------------------

    protected static function boot()
    {
        static::deleting(function ($account) {
            // Delete account data
            $account->accountData()->delete();
        });
    }
}
