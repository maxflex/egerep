<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class VariablesProvider extends ServiceProvider
{

    const VARIABLES = [
        'js' => [
            'bootbox',
            'inputmask',
            'mask',
            'engine',
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
            $view->with([
                'notifications_count' => \App\Models\Notification::countUnapproved(),
                'missed_calls_count'  => 0 // временно убираем \App\Models\Service\Call::countMissed()
            ]);
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
