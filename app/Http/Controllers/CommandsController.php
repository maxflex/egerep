<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tutor;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class CommandsController extends Controller
{
    public function getNumbers()
    {
        $tutors = Tutor::all();

        $tutor_ids = [];

        foreach ($tutors as $tutor) {
            preg_match("/([\d]{7})/imu", $tutor->contacts, $numbers);
            if (count($numbers)) {
	            echo $tutor->contacts . '<br>';
                // $tutor_ids[] = $tutor->id;
            }
        }

        dd($tutor_ids);
    }

    /**
     * Выдернуть номера телефонов из поля «Контакты»
     */
    public function getGrabPhonesSpace()
    {
        $tutors = Tutor::all();

        foreach ($tutors as $tutor) {
            preg_match_all("/([9]{1}[\d]{2}\s[\d]{7})/imu", $tutor->contacts, $phones);
            if ($phones[0]) {
                foreach ($phones[0] as $phone) {
                    $tutor->contacts = str_replace($phone, 'XXXXX', $tutor->contacts);
                    $phone = '7' . str_replace(' ', '', $phone);
                    $tutor->addPhone($phone);
                }
                $tutor->save();
            }
        }
    }

    /**
     * Выдернуть номера телефонов из поля «Контакты»
     */
    public function getGrabPhones()
    {
        $tutors = Tutor::all();

        foreach ($tutors as $tutor) {
            preg_match_all("/([4|9][\d]{9})/imu", $tutor->contacts, $phones);
            if ($phones[0]) {
                foreach ($phones[0] as $phone) {
                    $tutor->contacts = str_replace($phone, 'XXXXX', $tutor->contacts);
                    $phone = '7' . $phone;
                    $tutor->addPhone($phone);
                }
                $tutor->save();
            }
        }
    }

    /**
     * Cлияние опыта работы преподов с текушей работой
     */
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
