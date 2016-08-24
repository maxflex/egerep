<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    protected $fillable = ['number', 'user_id', 'id_status', 'id_status', 'id_smsru', 'mass', 'message'];
    protected $appends = ['user_login'];

    public function getUserLoginAttribute()
    {
        return User::where('id', $this->user_id)->select('login')->first()->login;
    }


	public static function sendToNumbers($numbers, $message, $mass) {
		foreach ($numbers as $number) {
			self::send($number, $message, $mass);
		}
	}


	public static function send($to, $message, $mass)
	{
		$to = explode(",", $to);
		foreach ($to as $number) {
			$number = cleanNumber($number);
			$number = trim($number);
			if (!preg_match('/[0-9]{10}/', $number)) {
				continue;
			}
			$params = array(
				"api_id"	=>	"8d5c1472-6dea-d6e4-75f4-a45e1a0c0653",
				"to"		=>	$number,
				"text"		=>	$message,
				"from"      =>  "EGE-Repetit",
			);
			$result = self::exec("http://sms.ru/sms/send", $params, $mass);
		}


		return $result;
	}

	protected static function exec($url, $params, $mass = false)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$result = curl_exec($ch);
		curl_close($ch);

		// Сохраняем отправленную смс
		$info = explode("\n", $result);

		$info = [
			"id_status" => $info[0],
			"id_smsru"	=> $info[1],
			"balance"	=> $info[2],
            "user_id"   => User::fromSession()->id,
			"message"	=> $params["text"],
			"number"	=> $params["to"],
            "mass"      => $mass,
		];

		// создаем объект для истории
		return SMS::create($info);
	}


	public function getStatus()
	{
		return static::textStatus($this->id_status);
	}

    /**
     * Получить по номеру телефона
     */
     public function scopeNumber($query, $number)
     {
         $number = cleanNumber($number);
         return $query->where('number', $number);
     }

	/**
	 * Получить текстовый статус в зависимости от когда СМС.
	 *
	 */
	public static function textStatus($sms_status)
	{
		// Статусы тут: http://sms.ru/?panel=api&subpanel=method&show=sms/status
		switch ($sms_status) {
			case -2 : return "не доставлено";
			case 100: return "в очереди";
			case 101: return "передается оператору";
			case 102: return "в пути";
			case 103: return "доставлено";
			case 104: return "время жизни истекло";
			case 105: return "удалено оператором";
			case 106: return "сбой в телефоне";
			case 107: return "не доставлено";
			case 108: return "отклонено";
			case 207: return "недопустимый номер";
			default:  return "неизвестно";
		}
	}
}
