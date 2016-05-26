angular
    .module 'Egerep'
    .factory 'Request', ($resource) ->
        $resource 'api/requests/:id', {},
            update:
                method: 'PUT'
    .controller 'RequestsIndex', ($rootScope, $scope, $timeout, $http, Request, RequestStates, Comment, PhoneService, UserService) ->
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true

        $scope.changeList = (state, state_id, push_history) ->
            $scope.chosen_id = state_id
            $scope.state_id = state_id
            $scope.current_page = 1

            loadRequests 1

            if push_history
                window.history.pushState(state, '', 'requests/' + state.constant.toLowerCase());


        $timeout ->
            loadRequests $scope.page
            $scope.current_page = $scope.page
            if not $scope.state_counts
                $http.post "api/requests/counts",
                    state: $scope.request_state
                .then (response) ->
                    $scope.request_state_counts = response.data.request_state_counts

        $scope.pageChanged = ->
            loadRequests $scope.current_page
            paginate('requests', $scope.current_page)

        loadRequests = (page) ->
            params = '?page=' + page
            if $scope.state_id
                params += '&state=' + $scope.state_id
            else
                params += '&state=' + 'new'

            $http.get "api/requests#{ params }"
            .then (response) ->
                $rootScope.frontendStop()
                $scope.data = response.data
                $scope.requests = $scope.data.data

    .controller 'RequestsForm', ($scope) ->
        console.log 'here'
