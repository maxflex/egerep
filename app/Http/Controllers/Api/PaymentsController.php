<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Payment\Source;
use DB;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Payment::search()->paginate(100);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return Payment::create($request->input())->fresh();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Payment::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Payment::find($id)->update($request->input());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Payment::destroy($id);
    }

    public function delete(Request $request)
    {
        foreach($request->ids as $id) {
            Payment::destroy($id);
        }
    }

    public function stats(Request $request)
    {
        switch($request->mode) {
            case 'by_days':
                $date_select = 'date';
                $date_field = 'date';
                break;
            case 'by_months':
                $date_select = "DATE_FORMAT(`date`, '%Y-%m') as month_date";
                $date_field = 'month_date';
                break;
        }
        $income = Payment::select(DB::raw("{$date_select}, sum(`sum`) as sum"))
            ->whereIn('addressee_id', $request->wallet_ids)->whereNotIn('source_id', $request->wallet_ids)
            ->orderByRaw("{$date_field} desc");
        $outcome = Payment::select(DB::raw("{$date_select}, sum(`sum`) as sum"))
            ->whereIn('source_id', $request->wallet_ids)->whereNotIn('addressee_id', $request->wallet_ids)
            ->orderByRaw("{$date_field} desc");
        $expenditures_income = Payment::selectRaw('expenditure_id as `id`, sum(`sum`) as sum, 1 as `is_income`')->whereIn('addressee_id', $request->wallet_ids)->whereNotIn('source_id', $request->wallet_ids)->groupBy('expenditure_id');
        $expenditures_outcome = Payment::selectRaw('expenditure_id as `id`, sum(`sum`) as sum, 0 as `is_income`')->whereIn('source_id', $request->wallet_ids)->whereNotIn('addressee_id', $request->wallet_ids)->groupBy('expenditure_id');
        if (isset($request->date_start) && $request->date_start) {
            $income->whereRaw("date(`date`) >= '" . fromDotDate($request->date_start) . "'");
            $outcome->whereRaw("date(`date`) >= '" . fromDotDate($request->date_start) . "'");
            $expenditures_income->whereRaw("date(`date`) >= '" . fromDotDate($request->date_start) . "'");
            $expenditures_outcome->whereRaw("date(`date`) >= '" . fromDotDate($request->date_start) . "'");
        }
        if (isset($request->date_end) && $request->date_end) {
            $income->whereRaw("date(`date`) <= '" . fromDotDate($request->date_end) . "'");
            $outcome->whereRaw("date(`date`) <= '" . fromDotDate($request->date_end) . "'");
            $expenditures_income->whereRaw("date(`date`) <= '" . fromDotDate($request->date_end) . "'");
            $expenditures_outcome->whereRaw("date(`date`) <= '" . fromDotDate($request->date_end) . "'");
        }
        if (isset($request->expenditure_ids) && count($request->expenditure_ids)) {
            $income->whereIn('expenditure_id', $request->expenditure_ids);
            $outcome->whereIn('expenditure_id', $request->expenditure_ids);
            $expenditures_income->whereIn('expenditure_id', $request->expenditure_ids);
            $expenditures_outcome->whereIn('expenditure_id', $request->expenditure_ids);
        }

        // ОСНОВНЫЕ ДАННЫЕ
        // определяем минимальную и максимальную даты
        // для создания интервала
        // @todo:  обработать corner-cases, когда нет какой-либо даты
        if ($request->mode == 'by_days') {
            $min_income_date = cloneQuery($income)->min($date_field);
            $max_income_date = cloneQuery($income)->max($date_field);
            $min_outcome_date = cloneQuery($outcome)->min($date_field);
            $max_outcome_date = cloneQuery($outcome)->max($date_field);
            $min_date = $min_income_date < $min_outcome_date ? $min_income_date : $min_outcome_date;
            $max_date = $max_income_date > $max_outcome_date ? $max_income_date : $max_outcome_date;
            // \end //

            $income = collect($income->groupBy(DB::raw($date_field))->get())->keyBy($date_field)->all();
            $outcome = collect($outcome->groupBy(DB::raw($date_field))->get())->keyBy($date_field)->all();
            $data = [];
            foreach(array_reverse(dateRange($min_date, $max_date)) as $date) {
                $date = dateFormat($date, true);
                $sum = 0;
                $in = 0;
                $out = 0;
                if (isset($income[$date])) {
                    $in = $income[$date]->sum;
                    $sum += $in;
                }
                if (isset($outcome[$date])) {
                    $out = $outcome[$date]->sum;
                    $sum -= $out;
                }
                $data[] = compact('date', 'sum', 'in', 'out');
            }
        } else {
            $income = collect($income->groupBy(DB::raw($date_field))->get())->keyBy($date_field)->all();
            $outcome = collect($outcome->groupBy(DB::raw($date_field))->get())->keyBy($date_field)->all();
            $dates = array_unique(array_merge(array_keys((array)$income), array_keys((array)$outcome)));
            if (! count($dates)) {
                return null;
            }
            sort($dates);
            $data = [];
            // foreach($dates as $date) {
            $d = new \DateTime($dates[0] . '-01');
            while ($d->format('Y-m') <= end($dates)) {
                $date = $d->format('Y-m');
                $sum = 0;
                $in = 0;
                $out = 0;
                if (isset($income[$date])) {
                    $in = $income[$date]->sum;
                    $sum += $in;
                }
                if (isset($outcome[$date])) {
                    $out = $outcome[$date]->sum;
                    $sum -= $out;
                }
                $data[] = compact('date', 'sum', 'in', 'out');
                $d->modify('first day of next month');
            }
            $data = array_reverse($data);
        }

        // EXPENDITURES
        $expenditure_data = array_merge($expenditures_income->get()->all(), $expenditures_outcome->get()->all());
        $expenditures = [];
        foreach($expenditure_data as $e) {
            if (! isset($expenditures[$e->id])) {
                $expenditures[$e->id] = ['in' => 0, 'out' => 0, 'sum' => 0];
            }
            if ($e->is_income) {
                $expenditures[$e->id]['in']  += $e->sum;
                $expenditures[$e->id]['sum'] += $e->sum;
            } else {
                $expenditures[$e->id]['out'] += $e->sum;
                $expenditures[$e->id]['sum'] -= $e->sum;
            }
        }

        return compact('data', 'expenditures');
    }


    public function remainders(Request $request)
    {
        $page = isset($request->page) ? $request->page : 1;
        $source = Source::find($request->source_id);

        // $sources = DB::table('payment_sources')->get();
        // $sources = DB::table('payment_sources')->whereId(1)->get();

        // кол-во элементов
        $query      = DB::table('payments')->where('date', '>=', $source->remainders->last()->getClean('date'))->whereRaw("(source_id={$source->id} or addressee_id={$source->id})");
        $item_cnt   = cloneQuery($query)->count();
        $items      = cloneQuery($query)->orderBy('date', 'desc')->take(Source::PER_PAGE_REMAINDERS)->skip(($page - 1) * Source::PER_PAGE_REMAINDERS)->get();

        // для inject входящий остаток
        $earliest_payment_date = cloneQuery($query)->orderBy('date', 'asc')->value('date');
        $latest_payment_date = cloneQuery($query)->orderBy('date', 'desc')->value('date');

        $items = collect($items)->groupBy('date')->all();

        // суммы дней
        $totals = [];
        foreach($items as $date => $data) {
            $remainder = $source->remainders->last();
            $sum = $remainder->remainder;
            if ($date > $remainder->getClean('date')) {
                $sum += Payment::where('addressee_id', $source->id)->where('date', '<=', $date)->where('date', '>', $remainder->getClean('date'))->sum('sum');
                $sum -= Payment::where('source_id', $source->id)->where('date', '<=', $date)->where('date', '>', $remainder->getClean('date'))->sum('sum');
            }
            // если date == source->remainder_date, то будет перезаписано ниже
            $totals[$date] = ['sum' => round($sum, 2), 'comment' => 'остаток на конец дня'];
        }

        // inject входящий остаток
        $dates = array_keys($items);
        $remainders = [];
        foreach($source->remainders as $r) {
            if (($r->getClean('date') >= $dates[count($dates) - 1] && $r->getClean('date') <= $dates[0])
                || ($r->getClean('date') < $earliest_payment_date) || ($r->getClean('date') > $latest_payment_date)
            ) {
                $remainders[$r->getClean('date')] = [
                    'sum'     => $r->remainder,
                    'comment' => ($r->date == $source->remainders->last()->date) ? 'входящий остаток' : 'засвидетельствованный остаток',
                ];
                if (! isset($items[$r->getClean('date')])) {
                    $items[$r->getClean('date')] = [];
                }
            }
        }

        return compact('items', 'totals', 'remainders', 'item_cnt');
    }
}
