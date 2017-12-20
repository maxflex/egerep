angular.module('Egerep').directive 'sms', ->
    restrict: 'E'
    templateUrl: 'directives/sms'
    scope:
        number: '='
    controller: ($scope, $timeout, Sms, PusherService) ->
        #pusher
        PusherService.bind 'SmsStatusUpdate', (data) ->
            angular.forEach $scope.history, (val, key) ->
                if val.external_id == data.external_id
                    val.id_status = data.id_status
                    $scope.$apply()
                console.log val, key

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
