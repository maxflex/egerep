<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\AccountPayment;
use App\Models\Account;
use App\Models\Helpers\MutualPayment;
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
	    $per_page = 30;

	    // получаем ВСЕ данные из account_payments и egecrm-payments
	    // мерджим, сортируем по дате и вырезаем пагинацию
        $account_payments = AccountPayment::orderBy('date', 'desc')->get()->all();
        $egecrm_payments = MutualPayment::query()->select(MutualPayment::defaultSelect())->orderBy(DB::raw("STR_TO_DATE(date, '%d.%m.%Y')", 'desc'))->get();

        // мердж
        $data = array_merge($account_payments, $egecrm_payments);

        // сортировка
		usort($data, function($a, $b) {
			return fromDotDate($a->date) < fromDotDate($b->date);
		});


		// вырезаем пагинацию
		$return = array_slice($data, (isset($request->page) ? ($request->page - 1) * $per_page : 0), $per_page);

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
        return AccountPayment::create($request->input());
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
