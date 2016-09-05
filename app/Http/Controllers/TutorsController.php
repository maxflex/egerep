<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tutor;
use App\Models\RequestList;
use App\Models\Service\Settings;
use DB;

class TutorsController extends Controller
{
    public function index(Request $request)
    {
        $search_text = $request->global_search;

        // поиск по клиентам (временно)
        if ($search_text[0] == 'c') {
            return view('clients.index')->with(
                ngInit([
                    'page'          => $request->input('page'),
                    'global_search' => $request->input('global_search'),
                ])
            );
        } else {
            return view('tutors.index')->with(
                ngInit([
                    'page'          => $request->input('page'),
                    'global_search' => $request->input('global_search'),
                    'tutor_errors_updated'  => Settings::get('tutor_errors_updated'),
                    'tutor_errors_updating' => Settings::get('tutor_errors_updating'),
                ])
            );
        }
    }

    public function create()
    {
        return view('tutors.create');
    }

    public function update(Request $request, $id)
    {
        Tutor::find($id)->update($request->input());
        return redirect()->route('tutors.index');
    }

    public function store(Request $request)
    {
        Tutor::create($request->input());
        return redirect()->route('tutors.index');
    }

    public function edit($id)
    {
        return view('tutors.edit')->with(compact('id'));
    }

    public function addToList($id)
    {
        $list   = RequestList::find($id);
        $client = $list->request->client;
        $search = [
            'state'    => ["5"],
            'subjects' => $list->subjects,
        ];

        if ($client->grade) {
            $search['grades'] = [(string)$client->grade];
        }

        return view('tutors.add-to-list.index')->with(ngInit([
            'search'       => $search,
            'list'         => $list,
            'client'       => $client,
            'request_id'   => $list->request->id,
        ]));
    }

    public function select()
    {
        return view('tutors.select.index');
    }
}
