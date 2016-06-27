angular
    .module 'Egerep'
    .controller 'ReviewsIndex', ($rootScope, $scope, $timeout, $http, Existance, ReviewStates, Presence, ReviewScores) ->
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true

        refreshCounts = ->
            $timeout ->
                $('.selectpicker option').each (index, el) ->
                    $(el).data 'subtext', $(el).attr 'data-subtext'
                    $(el).data 'content', $(el).attr 'data-content'
                $('.selectpicker').selectpicker 'refresh'
            , 100

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
                console.log response
                $scope.counts = response.data.counts
                $scope.data = response.data.data
                $scope.attachments = response.data.data.data
                $rootScope.frontend_loading = false
                refreshCounts()
