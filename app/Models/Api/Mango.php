<?php

namespace App\Models\Api;
use Carbon\Carbon;

class Mango {
	const API_URL		= 'https://app.mango-office.ru/vpbx/';
	const API_KEY		= 'goea67jyo7i63nf4xdtjn59npnfcee5l';
	const API_SALT		= 't9mp7vdltmhn0nhnq0x4vwha9ncdr8pa';

	# команды
	const COMMAND_HANGUP        = 'call/hangup';
    const COMMAND_REQUEST_STATS = 'stats/request';
    const COMMAND_GET_STATS     = 'stats/result';

    public static function run($command, $data)
    {
        return static::_run($command, $data);
    }

	/**
	 * Завершение вызова
	 */
	public static function hangup($call_id)
	{
		return static::_run(static::COMMAND_HANGUP, [
			'call_id' => $call_id,
		]);
	}

    /**
     * Запрос на генерацию статистики по номеру
     */
    public static function generateStats($number, $request_id)
    {
        return static::_run(static::COMMAND_REQUEST_STATS, [
            'date_from'  => Carbon::now()->subMonth()->timestamp,
            'date_to'    => Carbon::now()->timestamp,
            'from'       => [
                'number' => $number,
            ],
            'request_id' => $request_id,
        ]);
    }

    /**
     * Запрос на генерацию статистики по номеру
     */
    public static function getStats($key)
    {
        return static::_run(static::COMMAND_GET_STATS, compact('key'), false);
    }



	public static function call()
	{
	}

	/**
	 * Запустить команду
	 * @return результат $ch
	 */
	private static function _run($command, $data = [], $json_decode = true)
	{
		# command id неважно какой
		$data['command_id'] = 1;
		$json = json_encode($data);
		$sign = hash('sha256', static::API_KEY . $json . static::API_SALT);

		$post_data = [
			'vpbx_api_key'	=> static::API_KEY,
			'sign'			=> $sign,
			'json'			=> $json,
		];

		$post = http_build_query($post_data);

		$ch = curl_init(static::_command($command));

		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER 	=> true,
			CURLOPT_POST			=> true,
			CURLOPT_POSTFIELDS		=> $post,
		]);

		$response = curl_exec($ch);
		curl_close($ch);
		return $json_decode ? json_decode($response) : $response;
	}

	/**
	 * Создать URL с командой
	 */
	private static function _command($command)
	{
		return static::API_URL . 'commands/' . $command;
	}
}
