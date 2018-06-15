<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sms;
use App\Models\Tutor;

class SmsRating extends Model
{
    protected $table = 'sms_rating';
    public $timestamps = false;

    protected $fillable = [
        'call_date',
        'rating_date',
        'rating',
        'number',
        'user_id',
        'seconds',
        'mango_entry_id'
    ];

    /**
	 * Отправить ли СМС для оценки?
	 */
    public static function checkCall($piece_of_data)
    {
        // обрабатываем только звонки от ЕГЭ-Центра
		if ($piece_of_data['line_number'] == '74956468592') {
            $seconds = $piece_of_data['answer'] ? $piece_of_data['finish'] - $piece_of_data['answer'] : 0;
            // длительность более 2х минут
            if ($seconds > 120) {
                $number = $piece_of_data['from_number'];
                // если телефон мобильный
                if ($number[1] == '9') {
                    // если не было sms за последнюю неделю
                    if (! self::whereRaw("call_date > DATE_SUB(NOW(), INTERVAL 7 DAY) AND number='{$number}'")->exists()) {
                        // если звонок не от препода
                        if (! Tutor::findByPhone($number)->exists()) {
                            self::sendRateSms($piece_of_data);
                            // \Log::info("Incoming call from {$number} was {$seconds} seconds");
                        }
                    }
                }
            }
        }
    }

    /**
     * Отправить СМС с оценкой
     */
    public static function sendRateSms($piece_of_data)
    {
        Sms::send($piece_of_data['from_number'], 'Уважаемый клиент, оцените консультацию от 1 до 5 в ответном SMS на номер +79168877777. С уважением, ЕГЭ-Центр');
        self::create([
            'call_date' => date('Y-m-d H:i:s', $piece_of_data['start']),
            'number' => $piece_of_data['from_number'],
            'user_id' => $piece_of_data['to_extension'],
            'seconds' => $piece_of_data['answer'] ? $piece_of_data['finish'] - $piece_of_data['answer'] : 0,
            'is_incoming' => $piece_of_data['from_extension'] ? false : true,
            'mango_entry_id' => $piece_of_data['entry_id']
        ]);
    }

    /**
     * Принять оценку от клиента
     */
    public static function setRating($sms)
    {
        self::where('number', $sms->phone)->whereNull('rating')->update([
            'rating' => $sms->mes,
            'rating_date' => now()
        ]);
    }
}
