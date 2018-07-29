<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use DB;
use App\Models\Tutor;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class DebtController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // вечные должники
        return Tutor::where('debtor', 1)->get()->append(['last_account_info', 'debt_calc']);
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
        return Tutor::create($request->input())->fresh();
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
        Tutor::find($id)->update($request->input());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Tutor::destroy($id);
    }

    public function map(Request $request)
    {
        extract(array_filter($request->input('search')));

        // показывать в списке нужно преподавателей, у которых а) дебет не = 0 либо б) расчетный дебет не = 0
        $query = Tutor::with(['markers', 'plannedAccount'])->where('debtor', 0);
        $query->select(DB::raw("(select sum(debt) from debts where after_last_meeting=1 and tutor_id=tutors.id) as debt_calc"))
            ->having('debt_calc', '>', 0);

        if (isset($debt_calc_from)) {
            $query->having('debt_calc', '>=', $debt_calc_from);
        }

        if (isset($debt_calc_to)) {
            $query->having('debt_calc', '<=', $debt_calc_to);
        }

        if (isset($subjects)) {
            $sql = [];
            foreach ($subjects as $subject_id) {
                $sql[] = "FIND_IN_SET({$subject_id}, tutors.subjects)";
            }
            $query->whereRaw('(' . implode(' OR ', $sql) . ')');
        }

        if (isset($account_date_from) || isset($account_date_to)) {
            $query->join('accounts', function($join) {
                $join->on('accounts.tutor_id', '=', 'tutors.id')
                     ->on('accounts.date_end', '=', DB::raw('
                        (SELECT MAX(date_end)
                        FROM accounts a2
                        WHERE accounts.tutor_id = a2.tutor_id)
                    '));
            });
            if (isset($account_date_from)) {
                $query->where('accounts.date_end', '>=', fromDotDate($account_date_from));
            }
            if (isset($account_date_to)) {
                $query->where('accounts.date_end', '<=', fromDotDate($account_date_to));
            }
        }

        # выбираем только нужные поля для ускорения запроса
        $tutors = $query->addSelect([
            'tutors.id',
            'tutors.first_name',
            'tutors.last_name',
            'tutors.middle_name',
            'tutors.photo_extension',
            'tutors.birthday',
            'tutors.debt_comment',
            'tutors.in_egecentr',
        ])->get()->append('last_account_info');

        return $tutors;
    }
}
