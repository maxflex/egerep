<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tutor;

class UploadController extends Controller
{
    public function postTutor(Request $request)
    {
        $tutor_id = $request->input('tutor_id');
        $extension = $request->file('photo')->getClientOriginalExtension();
        Tutor::where('id', $tutor_id)->update(['photo_extension' => $extension]);
        $request->file('photo')->move(public_path() . Tutor::UPLOAD_DIR, $tutor_id . '_original.' . $extension);
        return $extension;
    }

    public function postCropped(Request $request)
    {
        $tutor = Tutor::find($request->input('tutor_id'));

        $file = $request->file('croppedImage');

        // Retina
        $img = new \abeautifulsite\SimpleImage($file);
        $img->resize(240, 300);
        $img->save(public_path() . Tutor::UPLOAD_DIR . $tutor->id . '@2x.' . $tutor->photo_extension);

        // Regular monitors
        $img = new \abeautifulsite\SimpleImage($file);
        $img->resize(120, 150);
        $img->save(public_path() . Tutor::UPLOAD_DIR . $tutor->id . '.' . $tutor->photo_extension);
    }
}
