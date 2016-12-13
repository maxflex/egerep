angular.module('Egerep').directive 'sms', ->
    restrict: 'E'
    templateUrl: 'directives/sms'
    scope:
        number: '='
        templateType: '='
    controller: ($scope, $timeout, Sms, $http) ->
        # включен ли вывод шаблона сообщений
        #if !$scope.templateType
        $scope.templateType = 1
        $http.get 'api/template/' + $scope.templateType
        .then (success) ->
            $scope.templates = success.data

        #установка сообщения из шаблона
        $scope.setMsg = (msg) ->
            $scope.message = msg

        # массовая отправка?
        $scope.mass = false

        # подсчитать СМС
        $scope.smsCount = ->
            SmsCounter.count($scope.message || '').messages

        # отправить
        $scope.send = ->
            if $scope.message
                $scope.sms_sending = true
                ajaxStart()
                sms = new Sms
                    message: $scope.message
                    to: $scope.number
                    mass: $scope.mass
                sms.$save()
                    .then (data) ->
                        ajaxEnd()
                        $scope.sms_sending = false
                        $scope.message = ''
                        $scope.history.push(data)
                        scrollDown()

        # подгружаем историю, если номер телефона меняется
        $scope.$watch 'number', (newVal, oldVal) ->
            console.log $scope.$parent.formatDateTime($scope.created_at)
            $scope.history = Sms.query({number: newVal}) if newVal
            scrollDown()

        scrollDown = ->
            $timeout ->
                $("#sms-history").animate({ scrollTop: $(window).height() }, "fast");