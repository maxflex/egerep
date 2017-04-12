<?php

namespace App\Models\Service;

class Youtube
{
    static $api_base = 'https://www.googleapis.com/youtube/v3/videos';
    static $thumbnail_base = 'https://i.ytimg.com/vi/';

    // @return - video duration in seconds
    public static function getVideoDuration($vid)
    {
        $params = array(
            'part' => 'contentDetails',
            'id' => $vid,
            'key' => config('youtube.api-key'),
        );

        $api_url = Youtube::$api_base . '?' . http_build_query($params);
        $result = json_decode(@file_get_contents($api_url), true);

        if(empty($result['items'][0]['contentDetails']))
            return null;
        $vinfo = $result['items'][0]['contentDetails'];

        $interval = new \DateInterval($vinfo['duration']);

        // возвратить время в секундах
        return ($interval->h * 3600 + $interval->i * 60 + $interval->s);
    }
}
