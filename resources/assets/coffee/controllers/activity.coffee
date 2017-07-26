angular
.module 'Egerep'
#
#   LIST CONTROLLER
#
.controller "ActivityIndex", ($scope, $http, $timeout, $rootScope, UserService) ->
    bindArguments($scope, arguments)

    $timeout ->
        $scope.search  = {}
        $scope.refreshCounts()

    $scope.formatMinutes = (minutes) ->
        format = if minutes >= 60 then 'H час m мин' else 'm мин'
        moment.duration(minutes, 'minutes').format(format)

    $scope.refreshCounts = ->
        $timeout ->
            $('.selectpicker option').each (index, el) ->
                $(el).data 'subtext', $(el).attr 'data-subtext'
                $(el).data 'content', $(el).attr 'data-content'
            $('.selectpicker').selectpicker 'refresh'
        , 300

    $scope.show = ->
        $rootScope.frontend_loading = true
        $http.get 'api/activity?' + $.param($scope.search)
            .then (response) ->
                $rootScope.frontend_loading = false
                $rootScope.frontendStop()
                $scope.data = response.data