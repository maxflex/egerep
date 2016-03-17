<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CommandsController extends Controller
{
    # слияние опыта работы преподов с текушей работой
    public function getMergeTeacherExeprience()
    {
        echo \DB::connection('egecrm')->update("
            UPDATE teachers
            SET experience = concat_ws('\n', experience, current_work)
            WHERE current_work != '' AND experience != ''
         ");

        echo ' | ';

        echo \DB::connection('egecrm')->update("
            UPDATE teachers
            SET experience = current_work
            WHERE current_work != '' AND experience = ''
         ");
    }
}
