<?php

namespace App\Models\Api;

class Api
{
    // Ключ апи
    const API_KEY = "44327d40af8a93c23497047c08688a50";

    // Куда отправлять запросы
    const API_URL = "http://lk2.ege-centr.ru:8085/api/";

     /**
      * Отправить запрос.
      *
      */
     public static function exec($function, $data)
     {
         // Добавляем API_KEY к запросу
         $data["API_KEY"] = static::API_KEY;

         $ch = curl_init();

         curl_setopt($ch, CURLOPT_URL, static::API_URL . $function);
         curl_setopt($ch, CURLOPT_POST, 1);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS,
                             http_build_query($data));
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

         $server_output = curl_exec($ch);

         curl_close($ch);

         return json_decode($server_output);
     }
}
