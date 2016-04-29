<?php

namespace App\Http\Controllers;

use App\Models\Tutor;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class UserstatsController extends Controller
{
    public function index()
    {
        $raw_stats = \DB::table('tutor_state_change')
                        ->select('date', 'user_id', 'tutors.state as state', DB::raw('count(tutors.state) as count'))
                        ->join('tutors', 'tutor_id', '=', 'tutors.id')
                        ->orderBy('date', 'desc')
                        ->orderBy('user_id')
                        ->orderBy('state')
                        ->groupBy(['date', 'user_id', 'state'])
//                        ->paginate(1  );
                        ->get();

        $stats = [];

        foreach ($raw_stats as $stat) {
            $stats[$stat->date][$stat->user_id][$stat->state] = $stat->count;
        }

        return view('userstats.index')->with(
            ngInit([
                'stats' => $stats,
            ])
        );
    }

    public static function transfer()
    {
        /* all tutors which aren't present in tutor_state_change table */
        $tutors = \DB::table('tutors')
                        ->select('id as tutor_id','responsible_user_id as user_id', DB::raw('DATE(NOW()) as date'))
                        ->where('responsible_user_id', '<>', '0')
                        ->whereRaw("not exists (select tsc.tutor_id from tutor_state_change tsc where tutors.id = tsc.tutor_id)")
                        ->get();

        $tutors = array_map(
                      function($tutor_as_class){
                          return (array) $tutor_as_class;
                      },
                      $tutors
                  );

        DB::table('tutor_state_change')->insert($tutors);
    }
}
