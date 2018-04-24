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
    public function index(Request $request)
    {
        $page = isset($request->page) ? $request->page : 1;

        // первую дату сделать либо сегодня либо первая загруженная картинка
        $first_bg_date = Background::orderBy('date', 'asc')->value('date');

        $d = new \DateTime(($first_bg_date && $first_bg_date < now(true)) ? $first_bg_date : '');

        if ($page > 1) {
            $d->modify('+' . (30 * ($page - 1)) . ' days');
        }

        $dates = [];

        $date_start = $d->format('Y-m-d');
        foreach(range(1, 30) as $i) {
            $dates[] = $d->format('Y-m-d');
            $d->modify('+1 day');
            $date_end = $d->format('Y-m-d');
        }

        $backgrounds = Background::whereBetween('date', [$date_start, $date_end])->get();

        return view('background.index')->with(
            ngInit([
                'current_page' => $page,
                'dates' => $dates,
                'backgrounds' => $backgrounds->keyBy('date'),
            ])
        );
    }

    public function preview($id, Request $request)
    {
        $wallpaper = Background::find($id);
        if (! allowed(\Shared\Rights::ER_APPROVE_BACKGROUND) && $wallpaper->user_id != User::fromSession()->id && !($wallpaper->status == 1 && $wallpaper->date <= now(true))) {
            return view('errors.not_allowed');
        }
        return view('login.login', [
            'wallpaper' => $wallpaper,
            'preview'   => true,
            'type'      => $request->type,
        ]);
    }
}
