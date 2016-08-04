<?php

namespace App\Http\Controllers;

use App\Models\Service\AttachmentError;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Service\Settings;

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
                'page' => $request->input('page'),
            ])
        );
    }

    public function errors(Request $request)
    {
        return view('attachments.errors', [
            'errors' => AttachmentError::paginate(30),
        ])->with(
            ngInit([
                'attachment_errors_updated'  => Settings::get('attachment_errors_updated'),
                'attachment_errors_updating'  => intval(Settings::get('attachment_errors_updating')),
            ])
        );
    }
}
