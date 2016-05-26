angular
    .module 'Egerep'
    .factory 'Request', ($resource) ->
        $resource 'api/requests/:id', {},
            update:
                method: 'PUT'
    .controller 'RequestsIndex', ($rootScope, $scope, $timeout, $http, Request, RequestStates, Comment, PhoneService, UserService, Grades) ->
        _.extend RequestStates, { all : 'Все' }
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true

        $scope.changeList = (state_id) ->
            $scope.chosen_state_id = state_id
            $scope.current_page = 1

            ajaxStart()
            loadRequests 1
            ajaxEnd()

            window.history.pushState(state_id, '', 'requests/' + state_id.toLowerCase());


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
            if $scope.chosen_state_id
                params += '&state=' + $scope.chosen_state_id
            else
                params += '&state=' + 'new'

            $http.get "api/requests#{ params }"
            .then (response) ->
                $rootScope.frontendStop()
                $scope.data = response.data
                $scope.requests = $scope.data.data

    .controller 'RequestsForm', ($scope) ->
        console.log 'here'
