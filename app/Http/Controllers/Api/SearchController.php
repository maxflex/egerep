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
    public function search(Request $request){

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
                # поиск по ученикам
                $clients = DB::table('clients')->select('id', 'name')
                                ->where('name', 'LIKE', '%' . $query . '%')
                                ->orWhere('phone', 'LIKE', '%' . $query . '%')
                                ->orWhere('phone2', 'LIKE', '%' . $query . '%')
                                ->orWhere('phone3', 'LIKE', '%' . $query . '%')
                                ->orWhere('phone4', 'LIKE', '%' . $query . '%')
                                ->orWhere('email', 'LIKE', '%' . $query . '%')
                                ->take(30)
                                ->get();

                # поиск по по предователям
                $tutors = DB::table('tutors')->select('id', 'first_name', 'last_name', 'middle_name')
                                ->where('first_name', 'LIKE', '%' . $query . '%')
                                ->orWhere('last_name', 'LIKE', '%' . $query . '%')
                                ->orWhere('middle_name', 'LIKE', '%' . $query . '%')
                                ->orWhere('phone', 'LIKE', '%' . $query . '%')
                                ->orWhere('phone2', 'LIKE', '%' . $query . '%')
                                ->orWhere('phone3', 'LIKE', '%' . $query . '%')
                                ->orWhere('phone4', 'LIKE', '%' . $query . '%')
                                ->orWhere('email', 'LIKE', '%' . $query . '%')
                                ->take(30)
                                ->get();
                return [
                    'clients' => $clients,
                    'tutors' => $tutors,
                    'results' => count($tutors) + count($clients),
                    'query' => $query
                ];
            }else{
                return response($validator->errors()->all(), 400);
            }
    }
}