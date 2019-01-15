<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;

class Source extends Model
{
    const PER_PAGE_REMAINDERS = 100;

    protected $table = 'payment_sources';
    public $timestamps = false;
    protected $fillable = ['name', 'position'];

    public function remainders()
    {
        return $this->hasMany(SourceRemainder::class, 'source_id')->orderBy('date', 'desc');
    }

    public function getNearestRemainder($date)
    {
        foreach($this->remainders as $r) {
            if ($r->getClean('date') <= $date) {
                return $r;
            }
        }
        return null;
    }
}
