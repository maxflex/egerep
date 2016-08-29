<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class VariablesProvider extends ServiceProvider
{

    const VARIABLES = [
        'js' => [
            'moment.min',
            'bootbox',
            'inputmask',
            'mask',
            'engine',
            'laroute',
            'svgmap',
            'ngmap.min',
            'jquery.fileupload',
            'canvas-to-blob',
        ],
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('app', function($view) {
            foreach (self::VARIABLES as $var_name => $var_value) {
                $view->with($var_name, $var_value);
            }
            $view->with('attachment_count_plus',  \Storage::get('attachment_count_plus'));
            $view->with('attachment_count_minus', \Storage::get('attachment_count_minus'));
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
