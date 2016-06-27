angular
    .module 'Egerep'
    .controller 'ReviewsIndex', ($rootScope, $scope, $timeout, $http, Existance, ReviewStates, Presence, ReviewScores) ->
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = false

        $scope.filter = ->
            $.cookie("reviews", JSON.stringify($scope.search), { expires: 365, path: '/' });
            $scope.current_page = 1
            $scope.pageChanged()

        $timeout ->
            $scope.search = if $.cookie("reviews") then JSON.parse($.cookie("reviews")) else {}
            load $scope.page
            $scope.current_page = $scope.page

        $scope.pageChanged = ->
            $rootScope.frontend_loading = true
            load $scope.current_page
            paginate('reviews', $scope.current_page)

        load = (page) ->
            params = '?page=' + page

            $http.get "api/reviews#{ params }"
            .then (response) ->
                $scope.data = response.data
                $scope.attachments = $scope.data.data
                $rootScope.frontend_loading = false
