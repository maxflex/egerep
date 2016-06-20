<?php

namespace App\Models\Api;
use Carbon\Carbon;
use App\Models\User;

class Mango {
	const API_URL		= 'https://app.mango-office.ru/vpbx/';
	const API_KEY		= 'goea67jyo7i63nf4xdtjn59npnfcee5l';
	const API_SALT		= 't9mp7vdltmhn0nhnq0x4vwha9ncdr8pa';

	# команды
	const COMMAND_HANGUP        = 'call/hangup';
    const COMMAND_REQUEST_STATS = 'stats/request';
    const COMMAND_GET_STATS     = 'stats/result';

    # константы
    const TRIALS = 5; // попыток запроса статистики
    const SLEEP  = 2; // секунд между попытками

	# номер
	const NUMBER_EGE_REPETITOR = '74956461080';

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
    private static function _generateStats($number)
    {
        return static::_run(static::COMMAND_REQUEST_STATS, [
            'date_from'  => Carbon::now()->subMonth()->timestamp,
            'date_to'    => Carbon::now()->timestamp,
            'fields'     => 'records, start, finish, from_extension, from_number, to_extension, to_number, disconnect_reason',
            'call_party' => [
                'number' => $number,
            ],
        ])->key;
    }

    /**
     * Запрос на генерацию статистики по номеру
     */
    public static function getStats($number)
    {
		$number = cleanNumber($number);
        $key = static::_generateStats($number);

        $trial = 1; // первая попытка
        while ($trial <= static::TRIALS) {
            $data = static::_run(static::COMMAND_GET_STATS, compact('key'), false, true);
            if ($data['code'] == 200) {
                // return $data['response'];
                $response_lines = explode(PHP_EOL, $data['response']);
                // return $response_lines;
                $return = [];
                foreach ($response_lines as $index => $response_line) {
                    // echo $index;
                    $info = explode(';', $response_line);
                    if (count($info) > 1) {
						$disconnect_reason = filter_var($info[7], FILTER_SANITIZE_NUMBER_INT);

						if ($info[6] == static::NUMBER_EGE_REPETITOR && in_array($disconnect_reason, [1131, 1100, 1121])) {
							continue;
						}

                        $return[] = [
                            'recording_id'		=> trim($info[0], '[]'),
                            // 'recording_id'		=> $info[0],
                            'start'             => $info[1],
                            'finish'            => $info[2],
                            'from_extension'    => $info[3],
                            'from_number'       => $info[4],
                            'to_extension'      => $info[5],
                            'to_number'         => $info[6],
                            'disconnect_reason' => $disconnect_reason,
                            'seconds'           => $info[2] - $info[1],
                            'from_user'         => $info[3] ? User::find($info[3]) : null,
                            'from_user'         => $info[5] ? User::find($info[5]) : null,
                            'date_start'        => date('Y-m-d H:i:s', $info[1]),
                        ];
                    }
                }

				usort($return, function($a, $b) {
					return $b['start'] > $a['start'];
				});

                return $return;
            }
            $trial++;
            sleep(static::SLEEP);
        }
    }



	public static function call()
	{
	}

	/**
	 * Запустить команду
	 * @return результат $ch
	 */
	private static function _run($command, $data = [], $json_decode = true, $http_code = false)
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
        $code     = curl_getinfo($ch)['http_code'];

        curl_close($ch);

        $return = $json_decode ? json_decode($response) : $response;

        if ($http_code) {
            return [
                'code'      => $code,
                'response'  => $return,
            ];
        } else {
            return $return;
        }
	}

	/**
	 * Создать URL с командой
	 */
	private static function _command($command)
	{
		return static::API_URL . 'commands/' . $command;
	}
}
