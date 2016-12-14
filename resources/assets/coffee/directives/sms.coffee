angular.module('Egerep').directive 'sms', ->
    restrict: 'E'
    templateUrl: 'directives/sms'
    scope:
        number: '='
    controller: ($scope, $timeout, Sms, PusherService) ->
        #pusher
        PusherService.bind 'SmsStatusUpdate', (data) ->
            console.log 'message: ', data;
            console.log 'message: ', $scope.history;

            angular.forEach $scope.history, (val, key) ->
                if val.id_smsru == data.id_smsru
                    val.id_status = data.id_status
                    $scope.$apply()
                console.log val, key

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
