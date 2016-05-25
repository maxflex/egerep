<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Api;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Tutor;

class ExternalController extends Controller
{
    public function exec($function, Request $request)
    {
        $this->$function($request->input());
    }

    /**
     * Входящая заявка
     */
    public function request($data)
    {
        // создаем нового клиента
        $client = Client::create([
            'name'  => $data->name,
            'phone' => cleanNumber($data->phone, true),
        ]);

        $comment = $data->message . "

        Метро: " . $data->metro_name;

        // создаем заявку клиента
        $client->requests()->create([
            'comment'           => $comment,
        ]);
    }
}
