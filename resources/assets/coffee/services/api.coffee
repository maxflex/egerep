angular.module('Egerep')
    .service 'ApiService', ($http) ->
        this.metro = (fun, data) ->
            $http.post "api/metro/#{fun}", data
        this
