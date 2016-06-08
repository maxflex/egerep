angular.module('Egerep').directive 'phones', ->
    restrict: 'E'
    templateUrl: 'directives/phones'
    scope:
        entity: '='
        entityType: '@'
    controller: ($scope, $timeout, $rootScope, PhoneService, UserService) ->
        $scope.PhoneService = PhoneService
        $scope.UserService  = UserService

        console.log $scope.entityType

        # level depth
        $rootScope.dataLoaded.promise.then (data) ->
            $scope.level = $scope.entity.phones.length or 1

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

        # информация по api
        $scope.info = (number) ->
            $scope.api_number = number
            $scope.mango_info = null
            $('#api-phone-info').modal 'show'
            PhoneService.info(number).then (response) ->
                console.log response.data
                $scope.mango_info = response.data

        $scope.formatDateTime = (date) ->
            moment(date).format "DD.MM.YY в HH:mm"

        $scope.time = (seconds) ->
            moment({}).seconds(seconds).format("mm:ss")

        $scope.getNumberTitle = (number) ->
            return $scope.entityType if number is $scope.api_number
            return 'егэ-репетитор' if number is '74956461080'
            number
