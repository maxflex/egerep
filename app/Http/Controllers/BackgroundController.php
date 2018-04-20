<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Background;
use App\Models\User;

class BackgroundController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // первую дату сделать либо сегодня либо первая загруженная картинка
        $first_bg_date = Background::orderBy('date', 'asc')->value('date');

        $d = new \DateTime(($first_bg_date && $first_bg_date < now(true)) ? $first_bg_date : '');

        $dates = [];

        $date_start = $d->format('Y-m-d');
        foreach(range(1, 7 * 2) as $i) {
            $dates[] = $d->format('Y-m-d');
            $d->modify('+1 day');
            $date_end = $d->format('Y-m-d');
        }

        $backgrounds = Background::whereBetween('date', [$date_start, $date_end])->get();

        return view('background.index')->with(
            ngInit([
                'dates' =>$dates,
                'backgrounds' => $backgrounds->keyBy('date'),
            ])
        );
    }

    public function preview($id)
    {
        $wallpaper = Background::find($id);
        if (! allowed(\Shared\Rights::ER_APPROVE_BACKGROUND) && $wallpaper->user_id != User::fromSession()->id) {
            return view('errors.not_allowed');
        }
        return view('login.login', [
            'wallpaper' => $wallpaper,
            'preview'   => true
        ]);
    }
}
