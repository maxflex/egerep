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

fileFromBower = function(file) {
    return 'resources/assets/bower/' + file
}

 // Include JS from bower
 jsFromBower = function(scripts) {
     bower_scripts = []
     scripts.forEach(function(script) {
         bower_scripts.push("../bower/" + script + ".js")
     })
     return bower_scripts
 }

elixir(function(mix) {
    mix
        // .browserSync({
        //     port: 8081,
        //     proxy: 'localhost:8080'
        // })
        .sass('app.scss')
        .copy(fileFromBower('egerep-svg-metro/views/map.svg'), 'public/img/svg/map.svg')
        .coffee(['resources/assets/coffee/*.coffee', 'resources/assets/coffee/*/*.coffee'])
        .version(['css/app.css', 'js/app.js'])
        .scripts(jsFromBower([
            'jquery/dist/jquery',
            'jquery-ui/ui/minified/core.min',
            'jquery-ui/ui/minified/widget.min',
            'angular/angular.min',
            'angular-animate/angular-animate.min',
            'angular-sanitize/angular-sanitize.min',
            'angular-resource/angular-resource.min',
            'angular-aria/angular-aria.min',
            'angular-messages/angular-messages.min',
            'angular-material/angular-material.min',
            'angular-i18n/angular-locale_ru-ru',
            'nprogress/nprogress',
            'underscore/underscore-min',
            'bootstrap-select/dist/js/bootstrap-select',
            'bootstrap-datepicker/dist/js/bootstrap-datepicker',
            'bootstrap-datepicker/dist/locales/bootstrap-datepicker.ru.min',
            'jquery-ui/ui/core',
            'jquery-ui/ui/widget',
            'jquery-ui/ui/mouse',
            'jquery-ui/ui/sortable',
            'jquery-ui/ui/draggable',
            'jquery-ui/ui/droppable',
            'angular-ui-sortable/sortable.min',
            'angular-bootstrap/ui-bootstrap.min',
            'cropper/dist/cropper',
            'pusher/dist/pusher.min',
            'ladda/dist/spin.min',
            'ladda/dist/ladda.min',
            'angular-ladda/dist/angular-ladda.min',
            'remarkable-bootstrap-notify/dist/bootstrap-notify.min',
            'StickyTableHeaders/js/jquery.stickytableheaders',
            'jquery.floatThead/dist/jquery.floatThead.min',
            'jsSHA/src/sha256',
            'jquery.cookie/jquery.cookie',
            'moment/moment',
            'moment/locale/ru',
            'jquery-color-animation/jquery.animate-colors-min',
            'ace-builds/src/ace',
            'ace-builds/src/mode-html',
            'ace-builds/src/mode-json',
            'vue/dist/vue.min',
            'vue-resource/dist/vue-resource.min',
            'phoneapi/dist/js/pusher',
            'js-md5/build/md5.min',
            'moment-duration-format/lib/moment-duration-format',
            'egerep-svg-metro/scripts/svg'
        ]).concat(['resources/assets/js/*.js']), 'public/js/vendor.js');
});
