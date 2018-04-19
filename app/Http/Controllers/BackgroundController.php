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
        $d = (new \DateTime)->modify('-2 weeks');

        $dates = [];

        $date_start = $d->format('Y-m-d');
        foreach(range(1, 7 * 4) as $i) {
            $dates[] = $d->format('Y-m-d');
            $d->modify('+1 day');
            $date_end = $d->format('Y-m-d');
        }

        $backgrounds = Background::whereBetween('date', [$date_start, $date_end])->get();

        return view('background.index')->with(
            ngInit([
                'dates' => array_reverse($dates),
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
        return view('login.login', compact('wallpaper'));
    }
}
