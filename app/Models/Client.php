<?php

namespace App\Models;

use Log;
use App\Traits\Markerable;
use App\Traits\Person;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use Markerable;
    use Person;

    protected $with = ['requests', 'markers'];

    protected $fillable = [
        'name',
        'grade',
        'address',
        'requests',
        'markers',
        'id_a_pers', // @temp
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
