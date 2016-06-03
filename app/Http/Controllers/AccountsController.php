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
        $tutor = Tutor::find($id);
        $clients = $tutor->getAttachmenClients();
        $hidden_clients_count = $tutor->clientsCount(1);

        if (! count($clients) && !$hidden_clients_count) {
            return view('tutors.accounts.no_clients');
        }
        return view('tutors.accounts.index')->with(ngInit([
            'tutor_id'             => $id,
            'clients'              => $clients,
            'first_attachment_date'=> $tutor->getFirstAttachmentDate(),
            'date_limit'           => $tutor->getDateLimit(),
            'hidden_clients_count' => $hidden_clients_count,
        ]) + ['tutor' => $tutor]);
    }

    public function hidden($id)
    {
        $tutor = Tutor::find($id);
        $clients = $tutor->getAttachmenClients(1, true);

        if (! count($clients)) {
            return view('shared.empty', [
                'message' => 'у преподавателя нет скрытых клиентов'
            ]);
        }
        return view('tutors.accounts.hidden')->with(ngInit([
            'tutor_id'             => $id,
            'clients'              => $clients,
            'visible_clients_count' => $tutor->clientsCount(0),
        ]) + ['tutor' => $tutor]);
    }
}
