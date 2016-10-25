<?php

namespace App\Models\Api;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Service\Settings;
use DB;

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
            'fields'     => 'records, start, finish, from_extension, from_number, to_extension, to_number, disconnect_reason, answer',
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
						// если это входящий звонок и разговора не было, не выводить
						if (! empty($info[5]) && $info[8] == 0) {
							continue;
						}

                        $return[] = [
                            'recording_id'		=> trim($info[0], '[]'),
                            'start'             => $info[1],
                            'finish'            => $info[2],
                            'from_extension'    => $info[3],
                            'from_number'       => $info[4],
                            'to_extension'      => $info[5],
                            'to_number'         => $info[6],
                            'disconnect_reason' => $info[7],
                            'seconds'           => ($info[8] != 0 ? ($info[2] - $info[8]) : 0),
                            'from_user'         => $info[3] ? User::find($info[3]) : null,
                            'from_user'         => $info[5] ? User::find($info[5]) : null,
                            'date_start'        => date('Y-m-d H:i:s', $info[1]),
                        ];
                    }
                }

				usort($return, function($a, $b) {
					return $a['start'] > $b['start'];
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


	/**
	* Генерация статистики и синхронизация базы
	*/
   public static function sync()
   {
	   $key = static::_regenerateTodayStats();

	   $trial = 1; // первая попытка
	   while ($trial <= static::TRIALS) {
		   $data = static::_run(static::COMMAND_GET_STATS, compact('key'), false, true);
		   if ($data['code'] == 200) {
			   $response_lines = explode(PHP_EOL, $data['response']);
			   $return = [];
			   foreach ($response_lines as $index => $response_line) {
				   $info = explode(';', $response_line);
				   if (count($info) > 1) {
						$return[] = [
							'recording_id'		 => trim($info[0], '[]'),
							'start'              => $info[1],
							'finish'             => $info[2],
							'answer'			 => trim($info[8], "\r"),
							'from_extension'     => $info[3],
							'from_number'        => $info[4],
							'to_extension'       => $info[5],
							'to_number'          => $info[6],
							'disconnect_reason'  => $info[7],
							'entry_id'           => $info[9],
							'line_number'        => $info[10],
							'location'           => $info[11],
						];
				   }
			   }
               // сначала добавляем новые данные, потом удаляем старые
               // порядок важен, чтобы избежать несколькосекундную задержку
               $last_id = DB::table('mango')->latest('id')->value('id');
               DB::table('mango')->insert($return);
               DB::table('mango')->whereRaw("DATE(FROM_UNIXTIME(start)) = DATE(NOW()) AND id <= {$last_id}")->delete();
			   return;
		   }
		   $trial++;
		   sleep(static::SLEEP);
	   }
   }

	/**
     * Удаляет
     */
    private static function _regenerateTodayStats()
    {
        return static::_run(static::COMMAND_REQUEST_STATS, [
            'date_from'  => strtotime('today'),
            'date_to'    => time(),
            'fields'     => 'records, start, finish, from_extension, from_number, to_extension, to_number, disconnect_reason, answer, entry_id, line_number, location',
        ])->key;
    }
}
