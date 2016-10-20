angular.module 'Egerep'
    .directive 'securityNotification', ->
        restrict: 'E'
        scope:
            tutor: '='
        templateUrl: 'directives/security-notification'
        controller: ($scope, Tutor) ->
            $scope.toggleNotification = (index) ->
                security_notification = angular.copy($scope.tutor.security_notification)
                security_notification[index] = not security_notification[index]
                ajaxStart()
                Tutor.update
                    id: $scope.tutor.id
                    security_notification: security_notification
                , ->
                    ajaxEnd()
                    $scope.tutor.security_notification = angular.copy(security_notification)
