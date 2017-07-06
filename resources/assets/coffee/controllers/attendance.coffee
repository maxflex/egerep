angular
    .module 'Egerep'
    .controller 'Attendance', ($scope, $rootScope, $http, $timeout, Months, UserService) ->
        bindArguments($scope, arguments)

        $scope.getDays = ->
            _.range(1, 32)

        $scope.late = (time) -> time > '10:05'

        $scope.hasData = ->
            $scope.data && (Object.keys($scope.data).length > 0)

        $scope.$watch 'month', (newVal, oldVal) ->
            $rootScope.frontend_loading = true
            $http.post 'api/attendance', {month: newVal}
            .then (response) ->
                $rootScope.frontend_loading = false
                $scope.data = response.data