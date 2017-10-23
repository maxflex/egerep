<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;

class Source extends Model
{
    protected $table = 'payment_sources';
    public $timestamps = false;

    protected $fillable = ['name', 'remainder', 'position'];


    public function getInRemainderAttribute()
    {
        $remainder = @intval($this->remainder);
        $remainder += Payment::where('type', 0)->where('addressee_id', $this->id)->sum('sum');
        $remainder -= Payment::where('type', 0)->where('source_id', $this->id)->sum('sum');

        $remainder -= Payment::where('type', 2)->where('source_id', $this->id)->sum('sum');
        $remainder += Payment::where('type', 2)->where('addressee_id', $this->id)->sum('sum');

        return $remainder;
    }

    public function getLoanRemainderAttribute()
    {
        $remainder = $this->in_remainder;
        $remainder += Payment::where('type', 1)->where('addressee_id', $this->id)->sum('sum');
        $remainder -= Payment::where('type', 1)->where('source_id', $this->id)->sum('sum');
        return $remainder;
    }
}
