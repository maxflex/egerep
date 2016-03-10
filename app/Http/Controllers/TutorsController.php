<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tutor;
use App\Models\RequestList;

class TutorsController extends Controller
{
    public function index(Request $request)
    {
        return view('tutors.index')->with(
            ngInit([
                'page'      => $request->input('page'),
                'search'    => $request->input('text'),
            ])
        );
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
        // $nginit = ngInit([
        //     'tutor' => Tutor::find($id)
        // ]);
        return view('tutors.edit')->with(compact('id'));
    }

    public function addToList($id)
    {
        $list = RequestList::find($id);

        return view('tutors.add-to-list.index')->with(ngInit([
            'list'      => $list,
            'client'    => $list->request->client,
        ]));
    }
}
