<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

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
        $query = Tutor::with(['markers'])->where('debt', '>', 0);

        if (isset($debt_from)) {
            $query->where('debt', '>=', $debt_from);
        }

        if (isset($debt_to)) {
            $query->where('debt', '<=', $debt_to);
        }

        // if (isset($account_date_from)) {
        //     $query->join('accounts AS a1', function($join) {
        //         $join->on('a1.tutor_id', '=', 'tutors.id')
        //              ->on('a1.date_end', '=', DB::raw('
        //                 (SELECT MAX(date_end)
        //                 FROM accounts a2
        //                 WHERE a1.tutor_id = a2.tutor_id)
        //             '));
        //     });
        //     // $query->select(DB::raw('count(*) as user_count, status'))
        // }

        // if (isset($account_date_from)) {
        //     $query->select(DB::raw('count(*) as user_count, status'))
        // }

        # выбираем только нужные поля для ускорения запроса
        $tutors = $query->get([
            'id',
            'first_name',
            'last_name',
            'middle_name',
            'photo_extension',
            'birth_year',
            'debt',
            'debt_comment'
        ])->append('last_account_info');

        return $tutors;
    }
}
