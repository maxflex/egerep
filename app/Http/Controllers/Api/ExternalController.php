<?php

namespace App\Http\Controllers\Api;

use App\Events\SmsStatusUpdate;
use Illuminate\Http\Request;

use App\Models\Api;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Tutor;
use Illuminate\Support\Facades\Redis;

class ExternalController extends Controller
{
    const URL = "http://ege-repetitor.ru:8086/uploads/";

    public function exec($function, Request $request)
    {
        return $this->$function($request);
    }

    public function requestNew($data)
    {
        $phone = cleanNumber($data->phone);

		// Если в заявке номер телефона совпадает с номером телефона,
		// указанным в другой невыполненной заявке, то такие заявки нужно сливать
		$client = Client::findByPhone($phone);

		if (! $client->exists()) {
			// создаем нового клиента
	        $client = Client::create(compact('phone'));
		} else {
			$client = $client->orderBy('id', 'desc')->first();
		}

		// @todo: нужно сделать поиск среди всех заявок в списке "невыполненные"
		//		  а не только по заявкам клиента
		$new_request = $client->requests()->where('state', 'new');

		if ($new_request->exists()) {
			$new_request = $new_request->orderBy('created_at', 'desc')->first();
			$new_request->comment = "Репетитор " . $data->tutor_id . " " . $new_request->comment;
			$new_request->save();
		} else {
			$comment = breakLines([($data->tutor_id ? "Репетитор " . $data->tutor_id : null), $data->comment, "Имя: " . $data->name]);
			// создаем заявку клиента
	        $client->requests()->create([
	            'comment' 	=> $comment,
				'google_id'	=> $data->google_id,
	        ]);
		}
    }


    /**
     * Входящий препод новый
     */
    public function tutorNew($request)
    {
        \Log::info('here');
        $data = $request->input();

        if ($request->has('experience_years')) {
            $data['start_career_year'] = date('Y') - $data['experience_years'];
        }

        // согласие размещать анкету в психпортрет
        $data['impression'] = @$request->agree_to_publish ? ' я хотел бы получать учеников и даю согласие на публикацию анкеты на сайте ege-repetitor.ru' : 'я хотел бы получать учеников, но не хотел бы размещать анкету на сайте ege-repetitor.ru';

        if ($request->has('filename')) {
            $ext = @end(explode('.', $request->filename));
            $data['photo_extension'] = $ext;
        }

        $new_tutor = Tutor::create($data);

        // загружаем фото
        if ($request->has('filename')) {
            $file = file_get_contents(self::URL . $request->filename);
            file_put_contents(public_path() . Tutor::UPLOAD_DIR . $new_tutor->id . '_original.' . $ext, $file);
        }
    }

    /**
     * Обновить статусы СМС. На самом деле запускается не кроном, а сервисом sms.ru
     *
     */
    public function updateSmsStatus($request)
    {
        foreach ($request->data as $entry) {
            $lines = explode("\n",$entry);
            if ($lines[0] == "sms_status") {

                $sms_id 	= $lines[1];
                $sms_status = $lines[2];

                \App\Models\Sms::where('id_smsru', $sms_id)->update([
                    'id_status' => $sms_status
                ]);

				event(new SmsStatusUpdate($sms_id, $sms_status));

                // "Изменение статуса. Сообщение: $sms_id. Новый статус: $sms_status";
                // Здесь вы можете уже выполнять любые действия над этими данными.
            }
        }
        exit("100"); /* Важно наличие этого блока, иначе наша система посчитает, что в вашем обработчике сбой */
    }

    public function mangoStats($request)
    {
        return \App\Models\Api\Mango::getStats($request->number);
    }
}
