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
        $first_bg_date = '2018-04-20';
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

        foreach($backgrounds as &$background) {
            // можно ли удалять?
            if (allowed(\Shared\Rights::ER_APPROVE_BACKGROUND)) {
                $background->may_be_deleted = true;
            } else {
                if ($background->user_id == User::id()) {
                    if ($background->date <= now(true)) {
                        $background->may_be_deleted = false;
                    } else {
                        $background->may_be_deleted = $background->status == 1 ? false : true;
                    }
                } else {
                    $background->may_be_deleted = false;
                }
            }

            // название
            // 0 – не отображается
            // 1 - отображается, но не редактируется
            // 2 – отображается и редактируется
            if (allowed(\Shared\Rights::ER_APPROVE_BACKGROUND)) {
                $background->title_status = 2;
            } else {
                if ($background->user_id == User::id()) {
                    if ($background->status == 1) {
                        $background->title_status = 1;
                    } else {
                        $background->title_status = 2;
                    }
                } else {
                    if ($background->date <= now(true) && $background->status == 1) {
                        $background->title_status = 1;
                    } else {
                        $background->title_status = 0;
                    }
                }
            }

            // превью
            if (allowed(\Shared\Rights::ER_APPROVE_BACKGROUND) || $background->user_id == User::id()) {
                $background->preview = true;
            } else {
                $background->preview = ($background->date <= now(true) && $background->status == 1) ? true : false;
            }
        }

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
        if (! allowed(\Shared\Rights::ER_APPROVE_BACKGROUND) && $wallpaper->user_id != User::id() && !($wallpaper->status == 1 && $wallpaper->date <= now(true))) {
            return view('errors.not_allowed');
        }
        return view('login.login', [
            'wallpaper' => $wallpaper,
            'preview'   => true,
            'type'      => $request->type,
        ]);
    }
}
