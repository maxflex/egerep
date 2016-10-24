angular
    .module 'Egerep'
    .controller 'SummaryUsers', ($scope, $rootScope, $timeout, $http, UserService, RequestStates) ->
        bindArguments($scope, arguments)

        $timeout ->
            $('#change-user').selectpicker 'refresh'
        , 500

        $scope.update = ->
            $rootScope.frontend_loading = true
            $http.post 'api/summary/users', $scope.search
            .then (response) ->
                $rootScope.frontend_loading = false
                $scope.stats = response.data

        $scope.monthYear = (date) ->
            date = date.split(".")
    		date = date.reverse()
    		date = date.join("-")
            moment(date).format('MMMM YYYY')

    .controller 'SummaryIndex', ($rootScope, $scope, $http, $timeout, PaymentMethods) ->
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true
        $scope.debt_updating = false

        $scope.getSum = (summary) ->
            (parseInt(summary.sum) or 0) + (parseInt(summary.debt_sum) or 0)

        $scope.updateDebt = ->
            $scope.debt_updating = true
            $http.post 'api/command/recalc-debt'
                .then (response) ->
                    $scope.debt_updating = false
                    $scope.debt_updated = response.data.debt_updated
                    $scope.total_debt   = response.data.total_debt

        $timeout ->
            loadSummary $scope.page
            $scope.current_page = $scope.page

        getPrefix = ->
            prefix = if $scope.type is 'total' then '' else "/#{$scope.type}"

        $scope.pageChanged = ->
            ajaxStart()
            loadSummary $scope.current_page
            paginate 'summary' + getPrefix() + '/' + $scope.filter, $scope.current_page

        loadSummary = (page) ->
            params  = getPrefix()
            params += '?page='   + page
            params += '&filter=' + $scope.filter
            $http.post "api/summary#{ params }"
            .then (response) ->
                ajaxEnd()
                $rootScope.frontendStop()
                $scope.summaries = response.data
