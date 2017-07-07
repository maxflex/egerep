<?php

namespace App\Models\Service;

use GuzzleHttp\Client;

class Fingerscan
{
    static $user;
    static $session;

    /**
     * $date – объект с start и end
     */
    public static function get($date)
    {
        $date_obj = new \DateTime($date);
        $date_obj->modify('+1 day');

        self::startSession();

        $start = 0;
        $pagesize = 10;
        $data = [];
        $step = 0;
        do {
            $step++;
            $response = self::$session->get(self::url('query.cgi', [
                'userid' => '',
                'sdate' => $date,
                'edate' => $date_obj->format('Y-m-d'),
                'start' => $start,
                'pagesize' => $pagesize, // максимальный 40, как выяснилось
            ]));

            $response = simplexml_load_string($response->body);

            // bugfix
            // если возникла ошибка, перезапускаем сессию
            if (isset($response->code) && @$response->msg != 'end') {
                self::startSession();
                continue;
            }

            $start += $pagesize;

            if (! isset($response->code)) {
                try {
                    foreach($response->items->row as $row) {
                        $row = (object)(array)$row; // это обязательно, потому что тип объекта XMLData
                        $data[] = (object)[
                            'user_id' => $row->userid,
                            'date' => $row->date
                        ];
                    }
                } catch (\ErrorException $e) {}
            }
        } while (@$response->msg != 'end');

        \Log::info($response->msg . " | step {$step} ");

        // dd($data);
        $data = array_reverse($data);

        $return = [];
        // берем первую запись для каждого дня
        foreach($data as $d) {
            if (! isset($return[$d->user_id])) {
                $return[$d->user_id] = $d;
            }
        }

        return $return;
    }

    public static function startSession()
    {
        self::$session = new \Requests_Session(config('fingerscan.url'));
        $response = self::$session->get('chk.cgi?userid= ' . config('fingerscan.userid') . '&userpwd=' . config('fingerscan.userpwd'));
        self::$user = simplexml_load_string($response->body);
    }

    public static function url($url, $params = [])
    {
        $params = array_merge($params, [
            'user' => trim(self::$user->user),
            'userkey' => trim(self::$user->userkey)
        ]);
        return $url . '?' . http_build_query($params);
    }
}