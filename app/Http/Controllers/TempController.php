<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class TempController extends Controller
{
    public function index($year)
    {
        $data = DB::connection('egecrm')->table('visit_journal')->select('id_entity', 'id_subject')
            ->where('type_entity', 'STUDENT')->where('year', $year)->groupBy('id_entity', 'id_subject')->get();

        foreach($data as $d) {
            $d->dates = DB::connection('egecrm')->table('visit_journal')->where('type_entity', 'STUDENT')->where('year', $year)
                ->where('id_subject', $d->id_subject)->where('id_entity', $d->id_entity)->pluck('lesson_date');
        }

        return view('temp')->with(compact('data', 'year'));
    }
}
