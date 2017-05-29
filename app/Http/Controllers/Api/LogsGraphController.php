<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Log;
use DB;

class LogsGraphController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // период
        switch($request->period) {
            case 2:
                $hours = 24;
                break;
            case 3:
                $hours = (24 * 7);
                break;
            default:
                $hours = 6;
                break;
        }

        // @todo: сначала получить время последнего действия и отталкиваться от него
        $data = DB::table('logs')->whereIn('user_id', $request->user_ids)->whereRaw("created_at > DATE_SUB(NOW(), INTERVAL " . $hours . " HOUR)")
            ->select(DB::raw("user_id, created_at, DATE_FORMAT(`created_at`, '%H:%i') as t, count(*) as c"))
            ->groupBy(DB::raw("user_id, DATE_FORMAT(`created_at`, '%H:%i')"))
            ->get();


        $by_users = [];
        foreach($data as $d) {
            $by_users[$d->user_id][] = [
                'x' => $d->created_at,
                'y' => $d->c,
            ];
        }

        $return = [];

        foreach($by_users as $user_id => $data) {
            $user = dbEgecrm('users')->whereId($user_id)->select('login', 'color')->first();
            $return[] = [
                'backgroundColor' => $user->color,
                'borderColor' => $user->color,
                'label' => $user->login,
                'fill' => false,
                'data' => $data,
            ];
        }

        // за последние 6 часов
        return $return;
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
