angular
    .module 'Egerep'
    .controller 'SummaryIndex', ($rootScope, $scope, $http, $timeout, PaymentMethods) ->
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true
        $scope.debt_updating = false

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

        $scope.pageChanged = ->
            ajaxStart()
            loadSummary $scope.current_page

            paginate 'summary/' + ($scope.type ? $scope.type + '/' : '') + $scope.filter, $scope.current_page

        loadSummary = (page) ->
            params  = if $scope.type == 'payments' then '/' + $scope.type else ''
            params += '?page='   + page
            params += '&filter=' + $scope.filter

            $http.post "api/summary#{ params }"
            .then (response) ->
                ajaxEnd()
                $rootScope.frontendStop()
                $scope.summaries = response.data
