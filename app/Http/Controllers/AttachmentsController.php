<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
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
        $errors = [];

        foreach (Attachment::all() as $attachment) {
            $attachment_errors = $attachment->errors();
            if (count($attachment_errors)) {
                $errors[] = (object)[
                    'id'    => $attachment->id,
                    'link'  => $attachment->link,
                    'codes' => $attachment_errors,
                ];
            }
        }

        return view('attachments.errors')->with(compact('errors'));
    }
}
