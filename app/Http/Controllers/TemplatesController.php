<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;

class TemplatesController extends Controller
{
    /**
     * Получаем шаблоны
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $allTemplates = Template::all()
            ->toArray();

        return view('templates.index', [
            'nginit' => "allTemplates = " . json_encode($allTemplates)
        ]);
    }

    /**
     * Сохраняем результаты измненеия
     * @param Request $request
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function save(Request $request){
        # правила валидации
        $rules = [
            'allTemplates' => 'required'
        ];

        #текст для ошибок обработки валидации
        $messages = [
            'required' => 'Запрос не должен быть пустым',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {

            foreach($request->allTemplates as $row){
                Template::where('id', $row['id'])
                    ->update($row);
            }
        }else{
            return response($validator->errors()->all(), 400);
        }

        return $request->all();
    }
}
