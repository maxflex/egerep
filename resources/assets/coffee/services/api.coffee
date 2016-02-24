angular.module('Egerep')
    .service 'ApiService', ($http) ->
        this.exec = (fun, data) ->
            $http.post "api/external/#{fun}", data
        this
