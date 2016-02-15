angular
    .module 'Egerep'
    .controller 'LoginCtrl', ($scope, $http) ->
        $scope.checkFields = ->
            $http.post 'login',
                login: $scope.login
                password: $scope.password
            .then (response) ->
                if response.data is true
                    location.reload()
