<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Models\Payment\Source;
use App\Models\Payment\Expenditure;
use App\Models\Payment\ExpenditureGroup;
use DB;

class PaymentsController extends Controller
{
    const VIEWS_FOLDER = 'payments.';

    public function index(Request $request)
    {
        if (! allowed(9999)) {
            return view('errors.not_allowed');
        }

        return view(self::VIEWS_FOLDER . 'index')->with(ngInit([
            'current_page' => $request->page,
            'fresh_payment'=> new Payment,
            'sources'      => Source::orderBy('position')->select('id', 'name')->get(),
            'expenditures' => ExpenditureGroup::getAll(),
        ]));
    }

    public function remainders(Request $request)
    {
        if (! allowed(9999)) {
            return view('errors.not_allowed');
        }
        // кол-во элементов = кол-во дней с момента самого раннего состояния счета / Source::PER_PAGE_REMAINDERS
        // $earliest_remainder_date = Source::whereNotNull('remainder_date')->min('remainder_date');
        // $datediff = time() - $earliest_remainder_date;
        // $item_cnt = floor($datediff / (60 * 60 * 24) / Source::PER_PAGE_REMAINDERS);

        return view(self::VIEWS_FOLDER . 'remainders')->with(ngInit([
            // 'page' => $request->page,
            'sources' => Source::whereNotNull('remainder_date')->orderBy('position')->get(),
            'expenditures' => collect(Expenditure::get())->keyBy('id')
            // 'item_cnt' => $item_cnt,
        ]));
    }

    public function export()
    {
        if (! allowed(9999)) {
            return view('errors.not_allowed');
        }

        $payments = Payment::search(false)->get();

        $data = [];
        // $data[] = ['ID', 'источник', 'адресат', 'статья расхода', 'назначение', 'сумма', 'тип', 'дата', 'пользователь', 'создан', 'обновлен'];
        $data[] = ['id', 'source_id', 'addressee_id', 'expenditure_id', 'purpose', 'sum',  'checked', 'date', 'user_id', 'created_at', 'updated_at'];
        foreach($payments as $payment) {
            $data[] = [$payment->id, $payment->source_id, $payment->addressee_id, $payment->expenditure_id, $payment->purpose, $payment->sum, $payment->checked, $payment->date, $payment->user_id, $payment->created_at, $payment->updated_at];
        }

        \Excel::create('paystream_' . date('Y-m-d-H-i-s'), function($excel) use($data) {
            $excel->sheet('paystream', function($sheet) use($data) {
                $sheet->fromArray($data, null, 'A1', true, false);
            });
        })->export('xls');
    }


    public function import(Request $request)
    {
        if (! allowed(9999)) {
            return view('errors.not_allowed');
        }

        if ($request->hasFile('file')) {
            $data = [];
            \Excel::load($request->file('file'), function($reader) use (&$data) {
                foreach ($reader->all()->toArray() as $model) {
                    // \Log::info(json_encode($model));
                    $data[] = $model;
                }
            });

            $unique_ids = [
                'source_id'      => [],
                'addressee_id'   => [],
                'expenditure_id' => [],
                'user_id'        => []
            ];

            foreach($data as $index => $d) {
                foreach(['source_id', 'addressee_id', 'expenditure_id', 'user_id'] as $field) {
                    if ($d[$field] && ! in_array($d[$field], $unique_ids[$field])) {
                        $unique_ids[$field][] = $d[$field];
                    }
                    if (isset($d['action']) && $d['action'] == 'delete' && (! $d['id'] || ! DB::table('payments')->whereId($d['id'])->exists())) {
                        return response()->json("невозможно удалить #{$index}", 422);
                    }
                    if (isset($d['action']) && $d['action'] == 'add' && $d['id']) {
                        return response()->json("невозможно добавить #{$index}", 422);
                    }
                    if (! preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $d['date'])) {
                        return response()->json("неверный формат даты #{$index}", 422);
                    }
                    if ($d['sum'] && ! filter_var($d['sum'], FILTER_VALIDATE_FLOAT)) {
                        return response()->json("неверная сумма #{$index}", 422);
                    }
                    if ($d['id']) {
                        if (! preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) [0-9]{2}:[0-9]{2}:[0-9]{2}$/", $d['created_at'])) {
                            return response()->json("неверный формат created_at #{$index}", 422);
                        }
                        if (! preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) [0-9]{2}:[0-9]{2}:[0-9]{2}$/", $d['updated_at'])) {
                            return response()->json("неверный формат updated_at #{$index}", 422);
                        }
                    }
                    unset($data[$index]['']);
                }
            }

            $source_ids = array_unique(array_merge($unique_ids['source_id'], $unique_ids['addressee_id']));
            if (Source::whereIn('id', $source_ids)->count() != count($source_ids)) {
                return response()->json("неверный ID источника или адресата", 422);
            }

            if (Expenditure::whereIn('id', $unique_ids['expenditure_id'])->count() != count($unique_ids['expenditure_id'])) {
                return response()->json("неверный ID расхода", 422);
            }

            if (User::whereIn('id', $unique_ids['user_id'])->count() != count($unique_ids['user_id'])) {
                return response()->json("неверный ID пользователя", 422);
            }

            // валидация пройдена, импорт...
            foreach($data as $d) {
                if (isset($d['action']) && $d['action'] == 'delete') {
                    unset($d['action']);
                    DB::table('payments')->whereId($d['id'])->delete();
                } else
                if (isset($d['action']) && $d['action'] == 'add') {
                    unset($d['action']);
                    $d['created_at'] = now();
                    $d['updated_at'] = now();
                    DB::table('payments')->insert($d);
                } else {
                    unset($d['action']);
                    $d['updated_at'] = now();
                    DB::table('payments')->whereId($d['id'])->update($d);
                }
            }

            return response()->json(count($data), 200);
        }
        return abort();
    }
}
