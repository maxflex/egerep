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
        $PaymentsClass = PaymentsClass();
        return $PaymentsClass::search()->paginate(100);
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
        $PaymentsClass = PaymentsClass();
        return $PaymentsClass::create($request->input())->fresh();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $PaymentsClass = PaymentsClass();
        return $PaymentsClass::find($id);
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
        $PaymentsClass = PaymentsClass();
        $PaymentsClass::find($id)->update($request->input());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $PaymentsClass = PaymentsClass();
        $PaymentsClass::destroy($id);
    }

    public function delete(Request $request)
    {
        $PaymentsClass = PaymentsClass();
        $PaymentsClass::whereIn('id', $request->ids)->delete();
    }


    public function stats(Request $request)
    {
        $PaymentsClass = PaymentsClass();

        $income = $PaymentsClass::select(DB::raw("DATE_FORMAT(`date`, '%Y-%m') as month_date, sum(`sum`) as sum"))
            ->whereIn('addressee_id', $request->wallet_ids)->whereNotIn('source_id', $request->wallet_ids)
            ->groupBy(DB::raw("month_date"))->orderBy(DB::raw('month_date'));

        $outcome = $PaymentsClass::select(DB::raw("DATE_FORMAT(`date`, '%Y-%m') as month_date, sum(`sum`) as sum"))
            ->whereIn('source_id', $request->wallet_ids)->whereNotIn('addressee_id', $request->wallet_ids)
            ->groupBy(DB::raw("month_date"))->orderBy(DB::raw('month_date'));

        $expenditures_income = $PaymentsClass::selectRaw('expenditure_id as `id`, sum(`sum`) as sum, 1 as `is_income`')->whereIn('addressee_id', $request->wallet_ids)->whereNotIn('source_id', $request->wallet_ids)->groupBy('expenditure_id');
        $expenditures_outcome = $PaymentsClass::selectRaw('expenditure_id as `id`, sum(`sum`) as sum, 0 as `is_income`')->whereIn('source_id', $request->wallet_ids)->whereNotIn('addressee_id', $request->wallet_ids)->groupBy('expenditure_id');

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

        $income_loan = cloneQuery($income)->where('type', 1);
        $outcome_loan = cloneQuery($outcome)->where('type', 1);

        $income         = collect($income->where('type', 0)->get())->keyBy('month_date')->all();
        $outcome        = collect($outcome->where('type', 0)->get())->keyBy('month_date')->all();
        $income_loan    = collect($income_loan->get())->keyBy('month_date')->all();
        $outcome_loan   = collect($outcome_loan->get())->keyBy('month_date')->all();

        /** ПО СТАТЬЯМ РАСХОДА **/
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
        /** КОНЕЦ ПО СТАТЬЯМ РАСХОДА **/

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

            $sum_loan = 0;
            $in_loan = 0;
            $out_loan = 0;
            if (isset($income_loan[$date])) {
                $in_loan = $income_loan[$date]->sum;
                $sum_loan += $in_loan;
            }
            if (isset($outcome_loan[$date])) {
                $out_loan = $outcome_loan[$date]->sum;
                $sum_loan -= $out_loan;
            }
            $data[$d->format('Y')][] = compact('date', 'sum', 'in', 'out', 'in_loan', 'out_loan', 'sum_loan');
            $d->modify('first day of next month');
        }

        return compact('data', 'expenditures');
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
