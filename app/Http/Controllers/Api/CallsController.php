<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class CallsController extends Controller
{
    public function index(Request $request)
    {
        // $search = isset($_COOKIE['logs']) ? json_decode($_COOKIE['logs']) : (object)[];
        // $data = Log::search($search)->paginate(30);

        /**
         * 1. Придумать еще одну резку "преподаватель" (да-нет-все)
         * 2. Плагин прослушки разговора и отработать, чтобы не глючила
         * 3. При необходимости уменьшить фонт-сайз таблицы
         * 4. Метку "договоры": не включать договоры 17-18
         * 5. Проверить все срезки на адекватность (брать по 4-5 вариантов)
         */
        $query = dbEgecrm('mango')->orderBy('mango.id', 'desc');

        if ($request->type) {
            if ($request->type == 1) {
                $query->where('from_extension', 0);
            }
            if ($request->type == 2) {
                $query->where('to_extension', 0);
            }
        }

        if (! isBlank($request->status_1)) {
            $query->where('status_1', $request->status_1);
        }
        if (! isBlank($request->status_2)) {
            $query->where('status_2', $request->status_2);
        }
        if (! isBlank($request->status_3)) {
            $query->where('status_3', $request->status_3);
        }
        if (! isBlank($request->status_4)) {
            $query->where('status_4', $request->status_4);
        }

        if ($request->user_id) {
            $query->whereRaw("(from_extension={$request->user_id} or to_extension={$request->user_id})");
        }

        if ($request->line_number) {
            $query->where('line_number', $request->line_number);
        }

        $data = $query->paginate(100);

        $data->getCollection()->map(function ($d) {
            $d->statuses = array_keys(array_filter([$d->status_1, $d->status_2, $d->status_3, $d->status_4]));
        });

        return [
            'data' => $data,
        ];
    }
}
