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
        $client_ids = $tutor->getClientIds();

        if (! count($client_ids)) {
            return view('tutors.accounts.no_clients');
        }

        return view('tutors.accounts.index')->with(ngInit([
            'tutor'                 => Tutor::where('id', $id)->with(['accounts'])->first(),
            'client_ids'            => $tutor->getClientIds(),
            'first_attachment_date' => $tutor->getFirstAttachmentDate(),
        ]));
    }
}
