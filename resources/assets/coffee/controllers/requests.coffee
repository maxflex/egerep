angular
    .module 'Egerep'
    .factory 'Request', ($resource) ->
        $resource 'api/requests/:id', {},
            update:
                method: 'PUT'
    .controller 'RequestsIndex', ($rootScope, $scope, $timeout, $http, Request, Comment, PhoneService, UserService) ->
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true

        $timeout ->
            loadRequests $scope.page
            $scope.current_page = $scope.page

        $scope.pageChanged = ->
            loadRequests $scope.current_page
            paginate('requests', $scope.current_page)

        loadRequests = (page) ->
            params = '?page=' + page

            $http.get "api/requests#{ params }"
            .then (response) ->
                $rootScope.frontendStop()
                $scope.data = response.data
                $scope.requests = $scope.data.data

    .controller 'RequestsForm', ($scope) ->
        console.log 'here'
