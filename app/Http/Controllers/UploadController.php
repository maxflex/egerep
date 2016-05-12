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
        return [
            'extension' => $extension,
            'size'      => filesize(public_path() . Tutor::UPLOAD_DIR .  $tutor_id . '_original.' . $extension)
        ];
    }

    public function postCropped(Request $request)
    {
        $tutor = Tutor::find($request->input('tutor_id'));

        $file = $request->file('croppedImage');

        $img = new \abeautifulsite\SimpleImage($file);
        $img->resize(240, 300);
        $img->save($tutor->photoPath());
        
        return $tutor->photo_cropped_size;
    }
}
