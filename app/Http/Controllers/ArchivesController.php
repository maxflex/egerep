<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Service\Settings;

class ArchivesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @state  Attachment state.
     */
    public function index(Request $request)
    {
        return view('archives.index')->with(
            ngInit([
                'page' => $request->input('page')
            ])
        );
    }
}
