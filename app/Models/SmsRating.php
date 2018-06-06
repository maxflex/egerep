<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sms;

class SmsRating extends Model
{
    protected $table = 'sms_rating';

    /**
	 * Отправить ли СМС для оценки?
	 */
    public static function checkCall($piece_of_data)
    {
        // входящий?
		if (! $piece_of_data['from_extension']) {
            $seconds = $piece_of_data['answer'] ? $piece_of_data['finish'] - $piece_of_data['answer'] : 0;
            // длительность более 2х минут
            if ($seconds > 120) {
                // TODO если не было sms за последнюю неделю
                if (! self::whereRaw("call_date > DATE_SUB(NOW(), INTERVAL 7 DAY) AND number='" . $piece_of_data['from_number'] . "'")->exists()) {
                    self::sendRateSms($piece_of_data);

                    $number = $piece_of_data['from_number'];
                    \Log::info("Incoming call from {$number} was {$seconds} seconds");
                }
            }
        }
    }

    /**
     * Отправить СМС с оценкой
     */
    public static function sendRateSms($piece_of_data)
    {
        // Sms::send($piece_of_data['from_number'], 'Оцените консультацию от 1 до 5 в ответном SMS');
        self::create([
            'call_date' => now(true),
            'number' => $piece_of_data['from_number'],
            'user_id' => $piece_of_data['to_extension'],
            'mango_entry_id' => $piece_of_data['entry_id']
        ]);
    }
}
