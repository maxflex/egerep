(function () {

    var laroute = (function () {

        var routes = {

            absolute: true,
            rootUrl: 'http://localhost:8081/egerep/public/',
            routes : [{"host":null,"methods":["GET","HEAD"],"uri":"tutors","name":"tutors.index","action":"App\Http\Controllers\TutorsController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"tutors\/create","name":"tutors.create","action":"App\Http\Controllers\TutorsController@create"},{"host":null,"methods":["POST"],"uri":"tutors","name":"tutors.store","action":"App\Http\Controllers\TutorsController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"tutors\/{tutors}","name":"tutors.show","action":"App\Http\Controllers\TutorsController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"tutors\/{tutors}\/edit","name":"tutors.edit","action":"App\Http\Controllers\TutorsController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"tutors\/{tutors}","name":"tutors.update","action":"App\Http\Controllers\TutorsController@update"},{"host":null,"methods":["DELETE"],"uri":"tutors\/{tutors}","name":"tutors.destroy","action":"App\Http\Controllers\TutorsController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"requests","name":"requests.index","action":"App\Http\Controllers\RequestsController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"requests\/create","name":"requests.create","action":"App\Http\Controllers\RequestsController@create"},{"host":null,"methods":["POST"],"uri":"requests","name":"requests.store","action":"App\Http\Controllers\RequestsController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"requests\/{requests}","name":"requests.show","action":"App\Http\Controllers\RequestsController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"requests\/{requests}\/edit","name":"requests.edit","action":"App\Http\Controllers\RequestsController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"requests\/{requests}","name":"requests.update","action":"App\Http\Controllers\RequestsController@update"},{"host":null,"methods":["DELETE"],"uri":"requests\/{requests}","name":"requests.destroy","action":"App\Http\Controllers\RequestsController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"clients","name":"clients.index","action":"App\Http\Controllers\ClientsController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"clients\/create","name":"clients.create","action":"App\Http\Controllers\ClientsController@create"},{"host":null,"methods":["POST"],"uri":"clients","name":"clients.store","action":"App\Http\Controllers\ClientsController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"clients\/{clients}","name":"clients.show","action":"App\Http\Controllers\ClientsController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"clients\/{clients}\/edit","name":"clients.edit","action":"App\Http\Controllers\ClientsController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"clients\/{clients}","name":"clients.update","action":"App\Http\Controllers\ClientsController@update"},{"host":null,"methods":["DELETE"],"uri":"clients\/{clients}","name":"clients.destroy","action":"App\Http\Controllers\ClientsController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/tutors\/list","name":null,"action":"App\Http\Controllers\Api\TutorsController@lists"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/tutors","name":"api.tutors.index","action":"App\Http\Controllers\Api\TutorsController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/tutors\/create","name":"api.tutors.create","action":"App\Http\Controllers\Api\TutorsController@create"},{"host":null,"methods":["POST"],"uri":"api\/tutors","name":"api.tutors.store","action":"App\Http\Controllers\Api\TutorsController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/tutors\/{tutors}","name":"api.tutors.show","action":"App\Http\Controllers\Api\TutorsController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/tutors\/{tutors}\/edit","name":"api.tutors.edit","action":"App\Http\Controllers\Api\TutorsController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/tutors\/{tutors}","name":"api.tutors.update","action":"App\Http\Controllers\Api\TutorsController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/tutors\/{tutors}","name":"api.tutors.destroy","action":"App\Http\Controllers\Api\TutorsController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/requests","name":"api.requests.index","action":"App\Http\Controllers\Api\RequestsController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/requests\/create","name":"api.requests.create","action":"App\Http\Controllers\Api\RequestsController@create"},{"host":null,"methods":["POST"],"uri":"api\/requests","name":"api.requests.store","action":"App\Http\Controllers\Api\RequestsController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/requests\/{requests}","name":"api.requests.show","action":"App\Http\Controllers\Api\RequestsController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/requests\/{requests}\/edit","name":"api.requests.edit","action":"App\Http\Controllers\Api\RequestsController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/requests\/{requests}","name":"api.requests.update","action":"App\Http\Controllers\Api\RequestsController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/requests\/{requests}","name":"api.requests.destroy","action":"App\Http\Controllers\Api\RequestsController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/clients","name":"api.clients.index","action":"App\Http\Controllers\Api\ClientsController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/clients\/create","name":"api.clients.create","action":"App\Http\Controllers\Api\ClientsController@create"},{"host":null,"methods":["POST"],"uri":"api\/clients","name":"api.clients.store","action":"App\Http\Controllers\Api\ClientsController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/clients\/{clients}","name":"api.clients.show","action":"App\Http\Controllers\Api\ClientsController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/clients\/{clients}\/edit","name":"api.clients.edit","action":"App\Http\Controllers\Api\ClientsController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/clients\/{clients}","name":"api.clients.update","action":"App\Http\Controllers\Api\ClientsController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/clients\/{clients}","name":"api.clients.destroy","action":"App\Http\Controllers\Api\ClientsController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/users","name":"api.users.index","action":"App\Http\Controllers\Api\UsersController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/users\/create","name":"api.users.create","action":"App\Http\Controllers\Api\UsersController@create"},{"host":null,"methods":["POST"],"uri":"api\/users","name":"api.users.store","action":"App\Http\Controllers\Api\UsersController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/users\/{users}","name":"api.users.show","action":"App\Http\Controllers\Api\UsersController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/users\/{users}\/edit","name":"api.users.edit","action":"App\Http\Controllers\Api\UsersController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/users\/{users}","name":"api.users.update","action":"App\Http\Controllers\Api\UsersController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/users\/{users}","name":"api.users.destroy","action":"App\Http\Controllers\Api\UsersController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"directives\/{directive}","name":null,"action":"Closure"}],
            prefix: '',

            route : function (name, parameters, route) {
                route = route || this.getByName(name);

                if ( ! route ) {
                    return undefined;
                }

                return this.toRoute(route, parameters);
            },

            url: function (url, parameters) {
                parameters = parameters || [];

                var uri = url + '/' + parameters.join('/');

                return this.getCorrectUrl(uri);
            },

            toRoute : function (route, parameters) {
                var uri = this.replaceNamedParameters(route.uri, parameters);
                var qs  = this.getRouteQueryString(parameters);

                return this.getCorrectUrl(uri + qs);
            },

            replaceNamedParameters : function (uri, parameters) {
                uri = uri.replace(/\{(.*?)\??\}/g, function(match, key) {
                    if (parameters.hasOwnProperty(key)) {
                        var value = parameters[key];
                        delete parameters[key];
                        return value;
                    } else {
                        return match;
                    }
                });

                // Strip out any optional parameters that were not given
                uri = uri.replace(/\/\{.*?\?\}/g, '');

                return uri;
            },

            getRouteQueryString : function (parameters) {
                var qs = [];
                for (var key in parameters) {
                    if (parameters.hasOwnProperty(key)) {
                        qs.push(key + '=' + parameters[key]);
                    }
                }

                if (qs.length < 1) {
                    return '';
                }

                return '?' + qs.join('&');
            },

            getByName : function (name) {
                for (var key in this.routes) {
                    if (this.routes.hasOwnProperty(key) && this.routes[key].name === name) {
                        return this.routes[key];
                    }
                }
            },

            getByAction : function(action) {
                for (var key in this.routes) {
                    if (this.routes.hasOwnProperty(key) && this.routes[key].action === action) {
                        return this.routes[key];
                    }
                }
            },

            getCorrectUrl: function (uri) {
                var url = this.prefix + '/' + uri.replace(/^\/?/, '');

                if(!this.absolute)
                    return url;

                return this.rootUrl.replace('/\/?$/', '') + url;
            }
        };

        var getLinkAttributes = function(attributes) {
            if ( ! attributes) {
                return '';
            }

            var attrs = [];
            for (var key in attributes) {
                if (attributes.hasOwnProperty(key)) {
                    attrs.push(key + '="' + attributes[key] + '"');
                }
            }

            return attrs.join(' ');
        };

        var getHtmlLink = function (url, title, attributes) {
            title      = title || url;
            attributes = getLinkAttributes(attributes);

            return '<a href="' + url + '" ' + attributes + '>' + title + '</a>';
        };

        return {
            // Generate a url for a given controller action.
            // laroute.action('HomeController@getIndex', [params = {}])
            action : function (name, parameters) {
                parameters = parameters || {};

                return routes.route(name, parameters, routes.getByAction(name));
            },

            // Generate a url for a given named route.
            // laroute.route('routeName', [params = {}])
            route : function (route, parameters) {
                parameters = parameters || {};

                return routes.route(route, parameters);
            },

            // Generate a fully qualified URL to the given path.
            // laroute.route('url', [params = {}])
            url : function (route, parameters) {
                parameters = parameters || {};

                return routes.url(route, parameters);
            },

            // Generate a html link to the given url.
            // laroute.link_to('foo/bar', [title = url], [attributes = {}])
            link_to : function (url, title, attributes) {
                url = this.url(url);

                return getHtmlLink(url, title, attributes);
            },

            // Generate a html link to the given route.
            // laroute.link_to_route('route.name', [title=url], [parameters = {}], [attributes = {}])
            link_to_route : function (route, title, parameters, attributes) {
                var url = this.route(route, parameters);

                return getHtmlLink(url, title, attributes);
            },

            // Generate a html link to the given controller action.
            // laroute.link_to_action('HomeController@getIndex', [title=url], [parameters = {}], [attributes = {}])
            link_to_action : function(action, title, parameters, attributes) {
                var url = this.action(action, parameters);

                return getHtmlLink(url, title, attributes);
            }

        };

    }).call(this);

    /**
     * Expose the class either via AMD, CommonJS or the global object
     */
    if (typeof define === 'function' && define.amd) {
        define(function () {
            return laroute;
        });
    }
    else if (typeof module === 'object' && module.exports){
        module.exports = laroute;
    }
    else {
        window.laroute = laroute;
    }

}).call(this);

