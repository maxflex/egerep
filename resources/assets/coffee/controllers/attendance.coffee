angular
    .module 'Egerep'
    .controller 'Attendance', ($scope, $rootScope, $http, $timeout, Months) ->
        bindArguments($scope, arguments)

        $scope.getDays = ->
            _.range(1, 32)

        $scope.late = (time) ->
            time && parseInt(time.replace(/\D/g,'')) > 5

        $scope.formatYearMonth = (year_month) ->
            moment(year_month + '-01').format("MMMM YYYY")

        $scope.$watch 'month', (newVal, oldVal) ->
            $rootScope.frontend_loading = true
            $http.post 'api/attendance', {month: newVal}
            .then (response) ->
                $rootScope.frontend_loading = false
                $scope.data = response.data