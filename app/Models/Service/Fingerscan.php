<?php

namespace App\Models\Service;

use GuzzleHttp\Client;

class Fingerscan
{
    public static function get()
    {
        $jar = new \GuzzleHttp\Cookie\CookieJar;

        $client = new Client([
            // 'base_uri' => 'http://213.184.130.66:8081',
            // 'base_uri' => config('app.fingerscan-url')
            // 'base_uri' => 'http://kolyadin.com/',
            'base_uri' => 'http://192.168.0.218/',
            'debug' => true,
            'version' => 0.9,
            'cookies' => $jar,
        ]);

        // $response = $client->request('GET', '');
        //
        // return $response->getBody();

        $response = $client->get('chk.cgi?userid=69&userpwd=1840');

        return "</textarea>" . $response . "</textarea>";
    }
}