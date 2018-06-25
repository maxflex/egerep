angular.module('Egerep').directive 'phone', ->
    restrict: 'E'
    templateUrl: 'directives/phone'
    controller: ($scope, $timeout, $rootScope, PhoneService) ->
        $scope.PhoneService = PhoneService

        $scope.phone = ''

        $scope.phoneMaskControl = (event) ->
            $scope.phone = $(event.target).val()

        $scope.isFull = (number) ->
            return false if number is undefined or number is ""
            !number.match(/_/)

        # отправить смс
        $scope.ssms = (number) ->
            $('#sms-modal').modal 'show'
            $rootScope.sms_number = number
