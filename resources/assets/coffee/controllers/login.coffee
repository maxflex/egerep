angular
    .module 'Egerep'
    .controller 'LoginCtrl', ($scope, $http) ->
        angular.element(document).ready ->
            $scope.l = Ladda.create(document.querySelector('#login-submit'))
            
        #обработка события по enter в форме логина
        $scope.enter = ($event) ->
            if $event.keyCode == 13
                $scope.checkFields()

        $scope.checkFields = ->
            $scope.l.start()
            ajaxStart()
            $scope.in_process = true
            $http.post 'login',
                login: $scope.login
                password: $scope.password
            .then (response) ->
                if response.data is true
                    location.reload()
                else
                    $scope.in_process = false
                    ajaxEnd()
                    $scope.l.stop()
                    notifyError "Неправильная пара логин-пароль"