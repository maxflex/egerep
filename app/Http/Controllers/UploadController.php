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

    /**
     * tutor file
     */
    public function postTutorfile(Request $request)
    {
        $tutor_id = $request->input('tutor_id');
        $file = uniqid() . '.' . $request->file('file')->getClientOriginalExtension();
        // Tutor::where('id', $tutor_id)->update(['file' => $file]);
        $request->file('file')->move(public_path() . Tutor::FILE_UPLOAD_DIR, $file);
        return [
            'file' => $file,
            // 'size'      => filesize(public_path() . Tutor::UPLOAD_DIR .  $tutor_id . '_original.' . $extension)
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
        $file = $request->file('photo');

        if ($file->getClientSize() > (1024 * 1024 * Background::MAX_SIZE)) { // 15 mb
            return response()->json(['error' => 'максимальный объём файла – ' . Background::MAX_SIZE . ' Мб']);
        }

        /** validations **/
        $min_width  = 3000;
        $min_height = 2000;

        list($width, $height) = getimagesize($file);

        if ($width < $min_width || $height < $min_height) {
            return response()->json(['error' => "минимальный размер изображения – {$min_width}x{$min_height}"]);
        }

        // 1 пользователь не может иметь Background::MAX_PER_USER и более изображений сегодня и в будущем,
        // поэтому на стадии попытки загрузить Background::MAX_PER_USER+1-е изображение не давать ему это делать
        if (Background::where('user_id', User::id())->where('date', '>=', now(true))->count() >= Background::MAX_PER_USER) {
            return response()->json(['error' => "вы достигли лимита по загруженным изображениям"]);
        }

        // все проверки пройдены
        $extension = $file->getClientOriginalExtension();

        if (! in_array($extension, ['jpg', 'jpeg'])) {
            return response()->json(['error' => "только файлы jpg/jpeg доступны для загрузки"]);
        }

        $filename = uniqid() . '.' . $extension;

        list($resampled_width, $resampled_height) = self::getSizedown($min_width, $min_height, $width, $height);

        $img = new \abeautifulsite\SimpleImage($file);
        $img->resize($resampled_width, $resampled_height);
        $img->save(public_path() . Background::UPLOAD_DIR . $filename, 70);

        $background = Background::create([
            'date' => $request->date,
            'user_id' => User::id(),
            'image' => $filename,
        ]);

        return $background;
    }

    // сжатие до минимальная ширина или высоты
    private static function getSizedown($min_width, $min_height, $width, $height)
    {
        // 6000 - 3000 = 3000
        $width_oversize = $width - $min_width;

        // высчитываем на сколько процентов уменьшили
        // width - 100%
        // width_oversize - x
        $w_percentage = $width_oversize / $width;

        // на такое же кол-во процентов уменьшаем высоту

        $new_height = $height - ($height * $w_percentage);

        if ($new_height > $min_height) {
            return [$min_width, $new_height];
        }


        $height_oversize = $height - $min_height;
        $h_percentage = $height_oversize / $height;
        $new_width = $width - ($width * $h_percentage);
        return [$new_width, $min_height];
    }
}
