angular
    .module 'Egerep'
    .controller 'SummaryIndex', ($rootScope, $scope, $http, $timeout) ->
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true
        $scope.debt_updating = false

        $scope.updateDebt = ->
            $scope.debt_updating = true
            $http.get 'api/command/recalc-debt'
                .then (response) ->
                    $scope.debt_updating = false

        $timeout ->
            loadSummary $scope.page
            $scope.current_page = $scope.page

        $scope.pageChanged = ->
            ajaxStart()
            loadSummary $scope.current_page
            ajaxEnd()

            paginate 'summary/' + $scope.filter, $scope.current_page

        loadSummary = (page) ->
            params  = '?page='   + page
            params += '&filter=' + $scope.filter

            $http.post "api/summary#{ params }"
            .then (response) ->
                $rootScope.frontendStop()
                $scope.summaries = response.data
