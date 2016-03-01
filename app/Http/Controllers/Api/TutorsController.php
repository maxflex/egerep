<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Tutor;
use Log;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class TutorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Tutor::paginate(30)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return Tutor::create($request->input());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Tutor::find($id)->toJson();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($request->input());
        Tutor::find($id)->update($request->input());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Get a list of only tutor_id => tutor full name
     */
     public function lists()
     {
         return Tutor::selectRaw("CONCAT_WS(' ', last_name, first_name, middle_name) as name, id")
            ->pluck('name', 'id');
     }

     public function deletePhoto($id)
     {
        $tutor = Tutor::find($id);
        Tutor::where('id', $id)->update(['photo_extension' => '']);

        @unlink($tutor->photoPath());
        @unlink($tutor->photoPath('_original'));
        @unlink($tutor->photoPath('@2x'));
     }
}
