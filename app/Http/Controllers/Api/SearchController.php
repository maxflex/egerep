<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Tutor;
use Illuminate\Support\Facades\DB;
use Validator;

class SearchController extends Controller
{
    /**
     * @param Request $request
     */
    public function search(Request $request)
    {

        # правила валидации
        $rules = [
            'query' => 'required'
        ];

        # текст для ошибок обработки валидации
        $messages = [
            'required' => 'Запрос не должен быть пустым',
        ];

        # проверка
        $validator = Validator::make($request->all(), $rules, $messages);

        if (!$validator->fails()) {
            $query = trim($request->input('query'));
            # удаляем все тире, чтобы искалось по номеру телефона и так, и так
            $query = str_replace("-", "", $query);

            # очистка и разбиение запроса на ключевые слова и формирование текста для FULLTEXT
            $queryArray = explode(' ', $query);


            # поля по которым должен искать клиентский поиск
            $fieldsSearchClients = ['phone', 'phone2', 'phone3', 'phone4', 'email'];

            # поиск по ученикам
            $clientsDB = DB::table('clients')
                ->select('id');

            foreach ($queryArray as $word) {
                $clientsDB->where(function ($query) use ($word, $fieldsSearchClients) {
                    foreach ($fieldsSearchClients as $field) {
                        $query->orWhere($field, 'LIKE', '%' . $word . '%');
                    }
                    return $query;
                });
            }

            $clients = $clientsDB
                ->orderBy('id', 'desc')
                ->take(30)
                ->get();

            # поля по которым должен искать преподовательский поиск
            $fieldsSearchTutors = [
                'first_name',
                'last_name',
                'middle_name',
                'phone',
                'phone2',
                'phone3',
                'phone4',
                'email'
            ];

            # поиск по по предователям
            $tutorsDB = DB::table('tutors')->select('id', 'first_name', 'last_name', 'middle_name');
            foreach ($queryArray as $word) {
                $tutorsDB->where(function ($query) use ($word, $fieldsSearchTutors) {
                    foreach ($fieldsSearchTutors as $field) {
                        $query->orWhere($field, 'LIKE', '%' . $word . '%');
                    }
                    return $query;
                });
            }

            $tutors = $tutorsDB->take(30)
                ->orderBy('id')
                ->take(30)
                ->get();

            return [
                'clients' => $clients,
                'tutors' => $tutors,
                'results' => count($tutors) + count($clients),
                'query' => $query
            ];
        } else {
            return response($validator->errors()->all(), 400);
        }
    }
}
