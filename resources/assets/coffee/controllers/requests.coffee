angular
    .module 'Egerep'
    .factory 'Request', ($resource) ->
        $resource 'api/requests/:id', {},
            update:
                method: 'PUT'
    .controller 'RequestsIndex', ($rootScope, $scope, $timeout, $http, Request, RequestStates, Comment, PhoneService, UserService, Grades) ->
        _.extend RequestStates, { all : 'все' }
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true

        # track comment loading.
        $rootScope.loaded_comments = 0
        $scope.$watch () ->
            console.log $rootScope.loaded_comments
            $rootScope.loaded_comments
        , (val) ->
            console.log val
            $rootScope.frontend_loading = false if $scope.requests and $scope.requests.length == val
        # /track comment loading.

        $scope.changeList = (state_id) ->
            $scope.chosen_state_id = state_id
            $scope.current_page = 1
            $rootScope.loaded_comments = 0
            $rootScope.frontend_loading = true

            ajaxStart()
            loadRequests 1
            ajaxEnd()

            window.history.pushState('requests/' + state_id.toLowerCase(), '', 'requests/' + state_id.toLowerCase());


        $timeout ->
            loadRequests $scope.page
            $scope.current_page = $scope.page
            if not $scope.state_counts
                $http.post "api/requests/counts",
                    state: $scope.request_state
                .then (response) ->
                    $scope.request_state_counts = response.data.request_state_counts

        $scope.pageChanged = ->
            $rootScope.frontend_loading = true
            $rootScope.loaded_comments = 0
            loadRequests $scope.current_page
            paginate('requests/' + $scope.chosen_state_id, $scope.current_page)

        loadRequests = (page) ->
            $scope.chosen_state_id = 'new' if not $scope.chosen_state_id

            params = '?page=' + page
            params += '&state=' + $scope.chosen_state_id


            $http.get "api/requests#{ params }"
            .then (response) ->
                $scope.data = response.data
                $scope.requests = $scope.data.data
                
        $scope.toggleState = (request) ->
            request_cpy = angular.copy request
            $rootScope.toggleEnum request_cpy, 'state', RequestStates;

            $scope.Request.update
                id: request_cpy.id
                state: request_cpy.state
            , (response) ->
                $rootScope.toggleEnum request, 'state', RequestStates;

    .controller 'RequestsForm', ($scope) ->
        console.log 'here'
