<?php

namespace App\Models\Service;

use GuzzleHttp\Client;

class Fingerscan
{
    static $user;

    public static function get()
    {
        $session = new \Requests_Session(config('fingerscan.url'));

        $response = $session->get('chk.cgi?userid= ' . config('fingerscan.userid') . '&userpwd=' . config('fingerscan.userpwd'));

        self::$user = simplexml_load_string($response->body);

        $response = $session->get(self::url('query.cgi', [
            'sdate' => '2017-07-01',
            'edate' => '2017-07-09',
            'start' => 0,
            'pagesize' => 10,
            'userid' => '',
        ]));

        $response = simplexml_load_string($response->body);

        dd($response);
        // return "<textarea style='width: 100%; height: 500px'>" . $response->raw . "</textarea>";
    }

    public static function url($url, $params = [])
    {
        $params = array_merge([
            'user' => trim(self::$user->user),
            'userkey' => trim(self::$user->userkey)
        ], $params);

        return $url . '?' . http_build_query($params);
    }



}