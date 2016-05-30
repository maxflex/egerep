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
        return Tutor::where('debt', '>', 0)
                ->paginate(30, ['last_account_info'])
                ->toJson();
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
        Tutor::where('id', $id)->update($request->input());
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
        extract(array_filter($request->search));

        // анализируем только не закрытых преподавателей с метками
        $query = Tutor::with(['markers'])->where('tutors.debt', '>', 0);

        if (isset($debt_from)) {
            $query->where('tutors.debt', '>=', $debt_from);
        }

        if (isset($debt_to)) {
            $query->where('tutors.debt', '<=', $debt_to);
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
        $tutors = $query->get([
            'tutors.id',
            'tutors.first_name',
            'tutors.last_name',
            'tutors.middle_name',
            'tutors.photo_extension',
            'tutors.birth_year',
            'tutors.debt',
            'tutors.debt_comment'
        ])->append('last_account_info');

        return $tutors;
    }
}
