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
        $search = isset($_COOKIE['payments']) ? json_decode($_COOKIE['payments']) : (object)[];
        $search = filterParams($search);

        $query = Payment::orderBy('date', 'desc')->orderBy('id', 'desc');

        if (isset($search->source_ids) && count($search->source_ids)) {
            $query->whereIn('source_id', $search->source_ids);
        }

        if (isset($search->addressee_ids) && count($search->addressee_ids)) {
            $query->whereIn('addressee_id', $search->addressee_ids);
        }

        if (isset($search->expenditure_ids) && count($search->expenditure_ids)) {
            $query->whereIn('expenditure_id', $search->expenditure_ids);
        }

        // if (isset($search->date_start) && $search->date_start) {
        //     $query->whereRaw("date(`date`) >= '" . fromDotDate($search->date_start) . "'");
        // }
        //
        // if (isset($search->date_end) && $search->date_end) {
        //     $query->whereRaw("date(`date`) <= '" . fromDotDate($search->date_end) . "'");
        // }

        if (isset($search->type)) {
            $query->where('type', $search->type);
        }

        return $query->paginate(100);
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
        if (isset($request->create_loan) && $request->create_loan) {
            $loan = new Payment($request->input());
            $loan->type = 1;
            $buf = $loan->addressee_id;
            $loan->addressee_id = $loan->source_id;
            $loan->source_id = $buf;
            $loan->save();
        }
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


    public function stats(Request $request)
    {
        $income = Payment::select(DB::raw("DATE_FORMAT(`date`, '%Y-%m') as month_date, sum(`sum`) as sum"))
            ->whereIn('addressee_id', $request->wallet_ids)->whereNotIn('source_id', $request->wallet_ids)
            ->groupBy(DB::raw("month_date"))->orderBy(DB::raw('month_date'));

        $outcome = Payment::select(DB::raw("DATE_FORMAT(`date`, '%Y-%m') as month_date, sum(`sum`) as sum"))
            ->whereIn('source_id', $request->wallet_ids)->whereNotIn('addressee_id', $request->wallet_ids)
            ->groupBy(DB::raw("month_date"))->orderBy(DB::raw('month_date'));

        if (isset($request->date_start) && $request->date_start) {
            $income->whereRaw("date(`date`) >= '" . fromDotDate($request->date_start) . "'");
            $outcome->whereRaw("date(`date`) >= '" . fromDotDate($request->date_start) . "'");
        }

        if (isset($request->date_end) && $request->date_end) {
            $income->whereRaw("date(`date`) <= '" . fromDotDate($request->date_end) . "'");
            $outcome->whereRaw("date(`date`) <= '" . fromDotDate($request->date_end) . "'");
        }

        if (isset($request->expenditure_ids) && count($request->expenditure_ids)) {
            $income->whereIn('expenditure_id', $request->expenditure_ids);
            $outcome->whereIn('expenditure_id', $request->expenditure_ids);
        }

        $income = collect($income->get())->keyBy('month_date')->all();
        $outcome = collect($outcome->get())->keyBy('month_date')->all();

        $dates = array_unique(array_merge(array_keys((array)$income), array_keys((array)$outcome)));

        if (! count($dates)) {
            return null;
        }

        sort($dates);

        $return = [];

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
            $return[$d->format('Y')][] = compact('date', 'sum', 'in', 'out');
            $d->modify('first day of next month');
        }

        return $return;
    }


    public function remainders(Request $request)
    {
        $page = (isset($request->page) ? $request->page : 1) - 1;

        $sources = Source::get();

        $date = new \DateTime('today');
        $skip_days = $page * Source::PER_PAGE_REMAINDERS;

        $end_date   = clone $date->sub(new \DateInterval("P{$skip_days}D"));
        $start_date = clone $date->sub(new \DateInterval("P" . Source::PER_PAGE_REMAINDERS . "D"));

        $return = [];
        while ($start_date < $end_date) {
            $start = $start_date->modify('+1 day')->format('Y-m-d'); // переход на новую неделю
            $end   = $start_date->format('Y-m-d');
            $return_date = $end;
            foreach($sources as $source) {
                $return[$return_date][$source->id] = Source::getRemaindersOnDate($source, $return_date);
            }
        }

        return array_reverse($return);
    }
}
