<?php

namespace App\Service;

use GuzzleHttp\Client;

class YandexDirect
{
    const LOCALE = 'ru';
    const TRIALS = 50; // попыток запроса статистики
    const SLEEP  = 2; // секунд между попытками

    const COMPAIGN_ID = 28693312;

    // на этих площадках нельзя отключать показы
    const FORBIDDEN = ['mail.ru'];

    public static function excludeSites()
    {
        // какие нужно выключить
        $sites_to_exclude = self::getSitesToExlude();

        // какие уже выключены
        $already_excluded = self::getExludedSites();

        $sites = array_values(array_unique(array_merge($sites_to_exclude, $already_excluded)));

        $data = [
            'method' => 'update',
            'params' => [
                'Campaigns' => [
                    [
                        'Id' => self::COMPAIGN_ID,
                        'ExcludedSites' => ['Items' => $sites]
                    ]
                ],
            ]
        ];

        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://api.direct.yandex.com/json/v5/',
        ]);

        $result = $client->post('campaigns', [
            'headers' => [
                'Accept-Language' => 'ru',
                'Content-Type' => 'application/json; charset=utf-8',
                'Authorization' => 'Bearer ' . config('yandex.direct-token'),
                'Client-Login' => config('yandex.direct-login'),
            ],
            'json' => $data,
        ]);


        // dump($result->getBody()->getContents()); exit();

        $result = json_decode($result->getBody()->getContents());

        dump($result);
    }


    /**
     * Получить уже исключенные сайты
     */
    public static function getExludedSites()
    {
        $data = [
            'method' => 'get',
            'params' => [
                'SelectionCriteria' => [
                    'Ids' => [self::COMPAIGN_ID],
                ],
                'FieldNames' => ['ExcludedSites']
            ]
        ];

        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://api.direct.yandex.com/json/v5/',
        ]);

        $result = $client->post('campaigns', [
            'headers' => [
                'Accept-Language' => 'ru',
                'Content-Type' => 'application/json; charset=utf-8',
                'Authorization' => 'Bearer ' . config('yandex.direct-token'),
                'Client-Login' => config('yandex.direct-login'),
            ],
            'json' => $data,
        ]);


        // dump($result->getBody()->getContents()); exit();

        $result = json_decode($result->getBody()->getContents());

        return self::removeWWW($result->result->Campaigns[0]->ExcludedSites->Items);
    }

    /**
     * Создать отчет и получить сайты (площадки) по условию для исключения
     */
    private static function getSitesToExlude()
    {
        $client = new Client([
            'base_uri' => 'https://api.direct.yandex.com/v5/',
        ]);
        $xml = file_get_contents('../direct.xml');
        $trial = 0;
        do {
            $result = $client->post('reports', [
                'headers' => [
                    'Accept-Language' => self::LOCALE,
                    'Content-Type' => 'text/xml; charset=utf-8',
                    'Authorization' => 'Bearer ' . config('yandex.direct-token'),
                    'Client-Login' => config('yandex.direct-login'),
                ],
                'body' => $xml,
            ]);
            $trial++;
        } while ($trial < self::TRIALS && $result->getStatusCode() != 200);


        $response = (string)$result->getBody();

        $lines = explode("\n", $response);

        // $data = [];
        $sites = [];

        foreach($lines as $index => $line) {
            if ($index >= 2) {
                @list($placement, $conversions, $clicks, $ctr) = explode("\t", $line);
                $conversions = intval($conversions);
                $clicks = intval($clicks);
                $ctr = (float)$ctr;
                /**
                 * за все время было 3 и более кликов
                 * за все время CTR 2% и более
                 * за все время не было ни одной конверсии
                 */
                if (!$conversions && $ctr >= 2 && $clicks >=3) {
                    // $data[] = compact('placement', 'conversions', 'clicks', 'ctr');
                    if (! in_array($placement, self::FORBIDDEN)) {
                        $sites[] = $placement;
                    }
                }
            }
        }
        return self::removeWWW($sites);
    }

    private static function removeWWW($sites)
    {
        foreach($sites as &$site) {
            if (strpos($site, 'www.') === 0) {
                $site = substr($site, 4);
            }
        }
        return $sites;
    }
}