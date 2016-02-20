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
            'ngmap.min'
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
