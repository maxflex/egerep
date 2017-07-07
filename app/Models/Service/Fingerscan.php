<?php

namespace App\Models\Service;

use GuzzleHttp\Client;

class Fingerscan
{
    static $user;

    /**
     * $date – объект с start и end
     */
    public static function get($date)
    {
        $date_obj = new \DateTime($date);
        $date_obj->modify('+1 day');

        $session = new \Requests_Session(config('fingerscan.url'));

        $response = $session->get('chk.cgi?userid= ' . config('fingerscan.userid') . '&userpwd=' . config('fingerscan.userpwd'));

        self::$user = simplexml_load_string($response->body);

        $start = 0;
        $pagesize = 10;
        $data = [];

        do {
            $response = $session->get(self::url('query.cgi', [
                'userid' => '',
                'sdate' => $date,
                'edate' => $date_obj->format('Y-m-d'),
                'start' => $start,
                'pagesize' => $pagesize, // максимальный 40, как выяснилось
            ]));

            $start += $pagesize;

            $response = simplexml_load_string($response->body);

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
        } while (! isset($response->code));

        \Log::info($response->msg);
        
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

    public static function url($url, $params = [])
    {
        $params = array_merge($params, [
            'user' => trim(self::$user->user),
            'userkey' => trim(self::$user->userkey)
        ]);
        return $url . '?' . http_build_query($params);
    }
}