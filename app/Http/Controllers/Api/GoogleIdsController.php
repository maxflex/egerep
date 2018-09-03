<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Request as ClientRequest;

class GoogleIdsController extends Controller
{
    public function show(Request $request)
    {
        $google_ids = explode(' ', $request->google_ids);

        $result = [];
        $totals = [
            'requests' => 0,
            'commission' => 0
        ];
        foreach($google_ids as $google_id) {
            if (isset($result[$google_id])) {
                continue;
            }

            $query = ClientRequest::where('google_id', $google_id);

            if ($query->exists()) {
                $requests = $query->select('id', 'date')->get();
                $requests_string = implode(',', collect($requests)->pluck('id'));

                $commission = DB::select("SELECT round(sum(if(ad.commission > 0, ad.commission, 0.25 * ad.sum))) as `sum`
                    FROM request_lists rl
                    JOIN attachments a ON a.request_list_id = rl.id
                    JOIN account_datas ad ON ad.attachment_id = a.id
                    WHERE rl.request_id IN ({$requests_string})
                ")[0]->sum;

                $result[$google_id] = compact('requests', 'commission');
                $totals['requests'] += count($requests);
                $totals['commission'] += $commission;
            } else {
                $result[$google_id] = null;
            }
        }

        return compact('result', 'totals');
    }
}
