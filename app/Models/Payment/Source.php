<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;

class Source extends Model
{
    protected $table = 'payment_sources';
    public $timestamps = false;

    const PER_PAGE_REMAINDERS = 600;

    protected $fillable = ['name', 'remainder', 'remainder_date', 'position'];
    protected static $dotDates = ['remainder_date'];

    public function getCalcRemainderAttribute()
    {
        $remainder = @intval($this->remainder);
        $PaymentsClass = PaymentsClass();

        $remainder += $PaymentsClass::where('type', 0)->where('addressee_id', $this->id)->sum('sum');
        $remainder -= $PaymentsClass::where('type', 0)->where('source_id', $this->id)->sum('sum');

        $remainder -= $PaymentsClass::where('type', 2)->where('source_id', $this->id)->sum('sum');
        $remainder += $PaymentsClass::where('type', 2)->where('addressee_id', $this->id)->sum('sum');

        return $remainder;
    }

    public function getCalcLoanRemainderAttribute()
    {
        $remainder = $this->calc_remainder;
        $PaymentsClass = PaymentsClass();
        $remainder += $PaymentsClass::where('type', 1)->where('addressee_id', $this->id)->sum('sum');
        $remainder -= $PaymentsClass::where('type', 1)->where('source_id', $this->id)->sum('sum');
        return $remainder;
    }

    /**
     * Входящий и заемный остаток на дату
     */
    public static function getRemaindersOnDate($source, $date)
    {
        $remainder = @intval($source->remainder);
        $PaymentsClass = PaymentsClass();
        $remainder += $PaymentsClass::where('type', 0)->where('addressee_id', $source->id)->where('date', '<=', $date)->where('date', '>=', $source->remainder_date)->sum('sum');
        $remainder -= $PaymentsClass::where('type', 0)->where('source_id', $source->id)->where('date', '<=', $date)->where('date', '>=', $source->remainder_date)->sum('sum');

        $remainder -= $PaymentsClass::where('type', 2)->where('source_id', $source->id)->where('date', '<=', $date)->where('date', '>=', $source->remainder_date)->sum('sum');
        $remainder += $PaymentsClass::where('type', 2)->where('addressee_id', $source->id)->where('date', '<=', $date)->where('date', '>=', $source->remainder_date)->sum('sum');

        $loan_remainder = @intval($remainder);
        // $loan_remainder += $PaymentsClass::where('type', 1)->where('addressee_id', $source->id)->where('date', '<=', $date)->sum('sum');
        // $loan_remainder -= $PaymentsClass::where('type', 1)->where('source_id', $source->id)->where('date', '<=', $date)->sum('sum');

        return compact('remainder', 'loan_remainder');
    }
}
