<?php

namespace App\Http\Controllers\Api\Payments;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Payment\SourceRemainder;

class SourceRemaindersController extends Controller
{
    public function update(Request $request, $id)
    {
        SourceRemainder::find($id)->update($request->input());
    }
    public function store(Request $request)
    {
        return SourceRemainder::create($request->input())->fresh();
    }
    public function destroy($id)
    {
        SourceRemainder::destroy($id);
    }
}
