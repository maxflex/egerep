<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\AccountPayment;
use App\Models\Account;
use DB;

class AccountPaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $per_page = 50;

        $account_payments = AccountPayment::orderBy('date', 'desc');

        // фильр по пользователям
        if (isset($request->user_id) && ! isBlank($request->user_id)) {
            $account_payments->where('user_id', $request->user_id);
        }

        // фильтр по типам расчета
        if (isset($request->method) && ! isBlank($request->method)) {
            // взаимозачет
            if ($request->method == -1) {
                $account_payments->whereId(-1); // обнуляем результаты $account_payments, останутся только взаимозачёты
            } else {
                $account_payments->where('method', $request->method);
            }
        }

        // фильтр по статусам платежа
        if (isset($request->confirmed) && ! isBlank($request->confirmed)) {
            $account_payments->where('confirmed', $request->confirmed);
        }

        $data = $account_payments->orderBy('date', 'desc')->get()->all();

        $return = array_slice($data, ($request->page > 0 ? ($request->page - 1) * $per_page : 0), $per_page);

        // информация о преподе
        foreach($return as $r) {
            // если не установлен tutor_id, добавляем
            if (! isset($r->tutor_id)) {
                $r->tutor_id = Account::whereId($r->account_id)->value('tutor_id');
            }
            $r->tutor = DB::table('tutors')->whereId($r->tutor_id)->select('first_name', 'last_name', 'middle_name')->first();
        }

        return [
            'data'      => $return,
            'per_page'  => $per_page,
            'total'     => count($data),
            'last_page' => ceil(count($data) / $per_page)
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return AccountPayment::create($request->input())->fresh();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        AccountPayment::find($id)->update($request->input());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        AccountPayment::destroy($id);
    }
}
