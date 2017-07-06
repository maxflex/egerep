angular
    .module 'Egerep'
    .controller 'LoginCtrl', ($scope, $http) ->
        angular.element(document).ready ->
            $scope.l = Ladda.create(document.querySelector('#login-submit'))

        #обработка события по enter в форме логина
        $scope.enter = ($event) ->
            if $event.keyCode == 13
                $scope.checkFields()

        $scope.goLogin = ->
            ajaxStart()
            $http.post 'login',
                login: $scope.login
                password: $scope.password
                code: $scope.code
                captcha: grecaptcha.getResponse()
            .then (response) ->
                grecaptcha.reset()
                if response.data is true
                    location.reload()
                else if response.data is 'sms'
                    ajaxEnd()
                    $scope.in_process = false
                    $scope.l.stop()
                    $scope.sms_verification = true
                else
                    $scope.in_process = false
                    ajaxEnd()
                    $scope.l.stop()
                    notifyError "Неправильная пара логин-пароль"

        $scope.checkFields = ->
            $scope.l.start()
            $scope.in_process = true
            if grecaptcha.getResponse() is '' then grecaptcha.execute() else $scope.goLogin()