<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models;
use App\Http\Controllers\Controller;
use App\Models\Service\Log;
use DB;

class LogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $search = isset($_COOKIE['logs']) ? json_decode($_COOKIE['logs']) : (object)[];
        $data = Log::search($search)->paginate(30);
        $data->getCollection()->map(function ($log) {
            if (in_array($log->table, ['attachments', 'archives', 'clients', 'request_lists', 'tutors']) && $log->type != 'delete') {
                switch ($log->table) {
                    case 'attachments':
                        $log->link = 'attachment/' . $log->row_id;
                        break;
                    case 'archives':
                        $log->link = 'archive/' . $log->row_id;
                        break;
                    case 'clients':
                        $log->link = 'client/' . $log->row_id;
                        break;
                    case 'request_lists':
                        $log->link = 'request-list/' . $log->row_id;
                        break;
                    case 'requests':
                        $log->link = 'requests/' . $log->row_id . '/edit';
                        break;
                    case 'tutors':
                        $log->link = 'tutors/' . $log->row_id . '/edit';
                        break;
                }
            }
            return $log;
        });

        return [
            // 'counts' => Log::counts($search),
            'data'   => $data,
        ];
        // return Log::search($search)->paginate(30);
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

    public function graph()
    {
        $search = isset($_COOKIE['logs']) ? json_decode($_COOKIE['logs']) : (object)[];
        // ->groupBy(DB::raw("DATE_FORMAT(`created_at`, '%H:%i')"))
        $data = Log::search($search, 'asc')->select('created_at')->pluck('created_at');
        // $return = [];
        // foreach($data as $d) {
        //     $return[] = [
        //         'date'  => $d,
        //         'value' => 1,
        //     ];
        // }
        $user = dbEgecrm('users')->whereId($search->user_id)->select('login', 'color')->first();

        $red_indexes = [];
        if (count($data)) {
            foreach($data as $index => $d) {
                if (! $index) {
                    $date = new \DateTime($d);
                    continue; // пропускаем пер
                }
                \Log::info('Date:' . $date->format("Y-m-d H:i:s"));
                $interval = $date->diff(new \DateTime($d));
                // если в рамках дня
                if (($interval->h < 8) && !$interval->d && !$interval->m) {
                    // разница в минутах
                    $difference = ($interval->h * 60) + $interval->i;
                    if ($difference > 30) {
                        \Log::info("Difference between " . $date->format("Y-m-d H:i:s") . " and ". (new \DateTime($d))->format("Y-m-d H:i:s") . " is {$interval->d}d {$interval->h}h {$interval->i}m");
                        $red_indexes[] = $index;
                    }
                }
                $date = new \DateTime($d);
            }
        }

        // разница в днях между первым и последним действием для определения ширины
        $width = '100%';
        if (count($data) >= 2) {
            // $date из предыдущего foreach последний
            $difference_in_days = (new \DateTime($data[0]))->diff($date)->d;
            $width = ($difference_in_days * 500) + 'px';
        }

        // datasets
        $green_data = array_fill(0, count($data), 1);
        $red_data = array_fill(0, count($data), 0);

        foreach($red_indexes as $index) {
            $red_data[$index] = 1;
            $green_data[$index] = 0;
        }

        return [
            'width'     => $width,
            'labels'    => $data,
            'datasets'  => [
                [
                    'backgroundColor' => 'green',
                    // 'backgroundColor' => $user->color,
                    'label' => 'действия вовремя',
                    'borderWidth' => 0,
                    'data' => $green_data
                ],
                [
                    'label' => 'действия с опозданием',
                    'backgroundColor' => 'red',
                    'borderWidth' => 0,
                    'data' => $red_data
                ],
            ]
        ];
    }
}
