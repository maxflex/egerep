<?php

namespace App\Http\Controllers;

use App\Models\Service\AttachmentError;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AttachmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @state  Attachment state.
     */
    public function index(Request $request)
    {
        return view('attachments.index')->with(
            ngInit([
                'page'            => $request->input('page'),
            ])
        );
    }

    public function errors(Request $request)
    {
        return view('attachments.errors')->with([
            'errors' => AttachmentError::paginate(30),
        ]);
    }
}
