<?php

namespace App\Models;

use Log;
use App\Traits\Markerable;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use Markerable;

    protected $with = ['requests', 'markers'];

    protected $fillable = [
        'name',
        'phone',
        'phone2',
        'phone3',
        'grade',
        'address',
        'requests',
        'markers'
    ];

    public function requests()
    {
        return $this->hasMany('App\Models\Request');
    }

    public function setRequestsAttribute($value)
    {
        foreach ($value as $request) {
            Request::find($request['id'])->update($request);
        }
    }
}
