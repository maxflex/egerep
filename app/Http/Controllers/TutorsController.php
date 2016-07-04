<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tutor;
use App\Models\RequestList;
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
                    'debtor'        => $request->input('debtor'),
                    'global_search' => $request->input('global_search'),
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
        $list = RequestList::find($id);

        return view('tutors.add-to-list.index')->with(ngInit([
            'list'          => $list,
            'client'        => $list->request->client,
            'request_id'    => $list->request->id,
        ]));
    }
}
