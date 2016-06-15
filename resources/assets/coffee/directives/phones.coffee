angular.module('Egerep').directive 'phones', ->
    restrict: 'E'
    templateUrl: 'directives/phones'
    scope:
        entity: '='
    controller: ($scope, $timeout, $rootScope, PhoneService) ->
        $scope.PhoneService = PhoneService

        # level depth
        $rootScope.dataLoaded.promise.then (data) ->
            $scope.level = if $scope.entity.phones and $scope.entity.phones.length then $scope.entity.phones.length else 1

        $scope.nextLevel = ->
            $scope.level++

        $scope.phoneMaskControl = (event) ->
            el = $(event.target)
            # grabs string phone_2 from object model.phone2
            # so it can be accessible by key
            phone_id = el.attr('ng-model').split('.')[1]
            $scope.entity[phone_id] = $(event.target).val()

        $scope.isFull = (number) ->
            return false if number is undefined or number is ""
            !number.match(/_/)

        # отправить смс
        $scope.sms = (number) ->
            $('#sms-modal').modal 'show'
            $scope.$parent.sms_number = number
