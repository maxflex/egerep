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

        if (! count($clients)) {
            return view('tutors.accounts.no_clients');
        }
        return view('tutors.accounts.index')->with(ngInit([
            'tutor_id'  => $id,
            'clients'   => $clients,
        ]) + ['tutor' => $tutor]);
    }
}
