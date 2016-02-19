var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */
elixir(function(mix) {
    mix
        .browserSync({
            port: 8081,
            proxy: 'localhost:8080'
        })
        .sass('app.scss')
        .coffee(['resources/assets/coffee/*.coffee', 'resources/assets/coffee/*/*.coffee'])
        .scripts([
            '../bower/jquery/dist/jquery.js',
            '../bower/bootstrap/dist/js/bootstrap.min.js',
            '../bower/AdminLTE/dist/js/app.min.js',
            '../bower/jquery-ui/ui/minified/core.min.js',
            '../bower/jquery-ui/ui/minified/widget.min.js',
            '../bower/angular/angular.min.js',
            '../bower/angular-animate/angular-animate.min.js',
            '../bower/angular-sanitize/angular-sanitize.min.js',
            '../bower/angular-resource/angular-resource.min.js',
            '../bower/angular-aria/angular-aria.min.js',
            '../bower/angular-messages/angular-messages.min.js',
            '../bower/angular-material/angular-material.min.js',
            '../bower/angular-i18n/angular-locale_ru-ru.js',
            '../bower/nprogress/nprogress.js',
            '../bower/underscore/underscore-min.js',
            '../bower/bootstrap-select/dist/js/bootstrap-select.js',
            '../bower/bootstrap-datepicker/dist/js/bootstrap-datepicker.js',
        ], 'public/js/vendor.js');
});
