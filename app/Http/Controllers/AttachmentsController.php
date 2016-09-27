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
                'page'                       => $request->input('page'),
                'attachment_errors_updated'  => Settings::get('attachment_errors_updated'),
                'attachment_errors_updating' => Settings::get('attachment_errors_updating'),
            ])
        );
    }

    public function stats(Request $request, $month = null)
    {
        if ($month === null) {
            $month = date('n'); // по умолчанию берём текущий месяц
        }
        return view('attachments.stats')->with(
            ngInit([
                'month' => $month,
            ])
        );
    }

    // public function new(Request $request)
    // {
    //     return view('attachments.index')->with(
    //         ngInit([
    //             'page'                       => $request->input('page'),
    //             'attachment_errors_updated'  => Settings::get('attachment_errors_updated'),
    //             'attachment_errors_updating' => Settings::get('attachment_errors_updating'),
    //         ])
    //     );
    // }
}
