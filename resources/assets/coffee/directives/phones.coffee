angular.module('Egerep').directive 'phones', ->
    restrict: 'E'
    templateUrl: 'directives/phones'
    scope:
        entity: '='
    controller: ($scope, $timeout, $rootScope) ->
        # level depth on + (phone1, phone2, phone3)
        $rootScope.dataLoaded.promise.then (data) ->
            $scope.level = if $scope.entity.phone3 then 3 else if $scope.entity.phone2 then 2 else 1
        # $timeout ->
        #     $scope.level = if $scope.entity.phone3 then 3 else if $scope.entity.phone2 then 2 else 1
        # , 100

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

        $scope.isMobile = (number) ->
            parseInt(number[4]) is 9 or parseInt(number[1]) is 9

        # отправить смс
        $scope.sms = (number) ->
            $('#sms-modal').modal 'show'
            $scope.$parent.sms_number = number

        # позвонить
        $scope.call = (number) ->
            location.href = "sip:" + number.replace(/[^0-9]/g, '')
