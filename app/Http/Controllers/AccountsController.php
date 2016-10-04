<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tutor;
use App\Models\AccountData;

class AccountsController extends Controller
{
    public function index($id)
    {
        $data = $this->getData($id, [0, false, true]); // params in array: hidden, with_lesson_count, get_possible_archives
        if ($data === false) {
            return view('tutors.accounts.no_clients');
        }
        return view('tutors.accounts.index')->with(ngInit($data) + ['tutor' => $data['tutor']]);
    }

    public function hidden($id)
    {
        $data = $this->getData($id, [1, true, false]); // params in array: hidden, with_lesson_count, get_possible_archives
        if (! count($data['clients'])) {
            return view('shared.empty', [
                'message' => 'у преподавателя нет скрытых клиентов'
            ]);
        }
        return view('tutors.accounts.index')->with(ngInit($data) + ['tutor' => $data['tutor']]);
    }

    private function getData($id, $attachmentParams = [])
    {
        list($hidden, $with_lesson_count, $get_possible_archives) = $attachmentParams;

        $tutor = Tutor::find($id);
        $clients = $tutor->getAttachmenClients($hidden, $with_lesson_count, $get_possible_archives);
        $hidden_clients_count = $tutor->clientsCount(1);
        $visible_clients_count = $tutor->clientsCount(0, true);

        if (! count($clients) && !$hidden_clients_count) {
            return false;
        }
        return [
                'page'                 => ($hidden ? 'hidden' : 'visible'),
                'tutor_id'             => $id,
                'tutor'                => $tutor,
                'clients'              => $clients,
                'first_attachment_date'=> $tutor->getFirstAttachmentDate(),
                'date_limit'           => $tutor->getDateLimit(),
                'hidden_clients_count' => $hidden_clients_count,
                'visible_clients_count'=> $visible_clients_count,
        ];
    }
}
