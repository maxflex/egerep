<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tutor;

class TransferController extends Controller
{
    /**
     * Перенести всех преподавателей (+фио)
     */
    public function getRepetitors(Request $request)
    {
        extract($request->input());
        $teachers = \DB::connection('egerep')->select("select * from repetitors limit {$limit} offset {$offset}");

        $transfered = 0;
        foreach ($teachers as $teacher) {
            $tutor = Tutor::where('id_a_pers', $teacher->id);
            // если преподавателя нет в базе
            if (!$tutor->exists()) {
                Tutor::create([
                    'id_a_pers'     => $teacher->id,
                    'first_name'    => $teacher->firstname,
                    'last_name'     => $teacher->lastname,
                    'middle_name'   => $teacher->middlename,
                ]);
                $transfered++;
            }
        }

        dd($transfered);
    }

    public function getEducation(Request $request)
    {
        extract($request->input());
        $teachers = \DB::connection('egerep')->select("select * from repetitors limit {$limit} offset {$offset}");

        foreach ($teachers as $teacher) {
            $tutor = Tutor::where('id_a_pers', $teacher->id);
            if ($tutor->exists()) {
                $tutor = $tutor->update([
                    'education' => $teacher->university_end,
                ]);
            }
        }
    }
}
