<?php

namespace App\Http\Controllers\Api;

use App\Models\Template;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


class TemplatesController extends Controller
{
    /**
     * получаем список шаблонов по типу
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getTemplatesByType($type)
    {
        return Template::where('type', $type)->get();
    }


}
