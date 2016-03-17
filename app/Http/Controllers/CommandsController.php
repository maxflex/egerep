<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tutor;
use App\Traits\Person;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class CommandsController extends Controller
{
    /**
     * Найти дубликаты
     */
    public function getTutorDuplicates()
    {
        $tutors = Tutor::all();

        foreach ($tutors as $tutor) {
            foreach ($tutor->phones as $phone) {
                if (@$phone[1] == '9') {
                    $query = Tutor::whereRaw("
                        (phone='$phone' OR phone2='$phone' OR phone3='$phone' OR phone4='$phone')
                        AND id != $tutor->id
                    ");
                    if ($query->exists()) {
                        echo "Tutor $tutor->id is doubled on $phone <br>";
                        $tutor->is_doubled = true;
                        $tutor->save();
                        break;
                    }
                }
                if (@$phone[1] == '4') {
                    $last_7_digits = substr($phone, -7);
                    $query = Tutor::whereRaw("
                        ( phone RLIKE '74[0-9]{2}$phone' OR phone2 RLIKE '74[0-9]{2}$phone'
                            OR phone3 RLIKE '74[0-9]{2}$phone' OR phone4 RLIKE '74[0-9]{2}$phone'
                        ) AND id != $tutor->id
                    ");
                    if ($query->exists()) {
                        echo "Tutor $tutor->id is doubled on $phone <br>";
                        $tutor->is_doubled = true;
                        $tutor->save();
                    }
                }
            }
        }
    }

    /**
     * @delete
     */
    public function getNumbers()
    {
        $tutors = Tutor::all();

        $tutor_ids = [];

        foreach ($tutors as $tutor) {
            preg_match("/([\d]{7})/imu", $tutor->contacts, $numbers);
            if (count($numbers)) {
	            echo $tutor->contacts . '<br>';
                $tutor_ids[] = $tutor->id;
            }
        }

        dd($tutor_ids);
    }

    /**
     * Выдернуть номера телефонов c пробелом из поля «Контакты»
     */
    public function getGrabPhonesMoscow()
    {
        $tutors = Tutor::all();

        foreach ($tutors as $tutor) {
            preg_match_all("/(\b[\d]{7}\b)/imu", $tutor->contacts, $phones);
            if ($phones[0]) {
                foreach ($phones[0] as $phone) {
                    if (count($tutor->phones) >= 4) {
                        break;
                    }
                    $tutor->contacts = str_replace($phone, 'XXXXX', $tutor->contacts);
                    $phone = '7495' . $phone;
                    $tutor->addPhone($phone);
                }
                $tutor->save();
            }
        }
    }

    /**
     * Выдернуть номера телефонов c пробелом из поля «Контакты»
     */
    public function getGrabPhonesSpace()
    {
        $tutors = Tutor::all();

        foreach ($tutors as $tutor) {
            preg_match_all("/([9]{1}[\d]{2}\s[\d]{7})/imu", $tutor->contacts, $phones);
            if ($phones[0]) {
                foreach ($phones[0] as $phone) {
                    if (count($tutor->phones) >= 4) {
                        break;
                    }
                    $tutor->contacts = str_replace($phone, 'XXXXX', $tutor->contacts);
                    $phone = '7' . preg_replace('/\s+/', '', $phone);
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
                    if (count($tutor->phones) >= 4) {
                        break;
                    }
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
