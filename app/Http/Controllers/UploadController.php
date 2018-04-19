<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tutor;
use App\Models\User;
use App\Models\Background;

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

    public function postBackground(Request $request)
    {
        if ($request->file('photo')->getClientSize() > 12582912) { // 12 mb с запасом
            return response()->json(['error' => 'максимальный объём файла – 12 Мб']);
        }

        /** validations **/
        $min_height = 768;
        $min_width  = 1024;

        list($width, $height) = getimagesize($request->file('photo'));

        if ($width < $min_width || $height < $min_height) {
            return response()->json(['error' => "минимальный размер изображения – {$min_width}x{$min_height}"]);
        }


        $extension = $request->file('photo')->getClientOriginalExtension();
        $filename = uniqid() . '.' . $extension;
        $request->file('photo')->move(public_path() . Background::UPLOAD_DIR, $filename);

        $background = Background::create([
            'date' => $request->date,
            'user_id' => User::fromSession()->id,
            'image' => $filename,
        ]);

        return $background;
    }
}
