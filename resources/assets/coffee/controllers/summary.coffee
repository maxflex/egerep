angular
    .module 'Egerep'
    .controller 'SummaryIndex', ($rootScope, $scope, $http, $timeout) ->
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true

        $timeout ->
            loadSummary $scope.page
            $scope.current_page = $scope.page

        $scope.pageChanged = ->
            loadSummary $scope.current_page
            paginate 'summary/' + $scope.filter, $scope.current_page

        loadSummary = (page) ->
            params  = '?page='   + page
            params += '&filter=' + $scope.filter

            $http.post "api/summary#{ params }"
            .then (response) ->
                $rootScope.frontendStop()
                $scope.summaries = response.data