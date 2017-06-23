angular
    .module 'Egerep'
    .controller 'StreamIndex', ($rootScope, $scope, $timeout, $http, Subjects) ->
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true

        # $scope.recalcReviewErrors = ->
        #     $scope.review_errors_updating = true
        #     $http.post 'api/command/model-errors', {model: 'reviews'}
        #
        refreshCounts = ->
            $timeout ->
                $('.selectpicker option').each (index, el) ->
                    $(el).data 'subtext', $(el).attr 'data-subtext'
                    $(el).data 'content', $(el).attr 'data-content'
                $('.selectpicker').selectpicker 'refresh'
            , 100

        $scope.keyFilter = (event) ->
            $scope.filter() if event.keyCode is 13

        $scope.filter = ->
            $.cookie("stream", JSON.stringify($scope.search), { expires: 365, path: '/' });
            $scope.current_page = 1
            $scope.pageChanged()

        $timeout ->
            $scope.search = if $.cookie("stream") then JSON.parse($.cookie("stream")) else {}
            load $scope.page
            $scope.current_page = $scope.page

        $scope.pageChanged = ->
            $rootScope.frontend_loading = true
            load $scope.current_page
            paginate('stream', $scope.current_page)

        load = (page) ->
            params = '?page=' + page
            $http.get "api/stream#{ params }"
            .then (response) ->
                console.log response
                $scope.counts = response.data.counts
                $scope.data   = response.data.data
                $scope.stream = response.data.data.data
                $rootScope.frontend_loading = false
                refreshCounts()
    .controller 'StreamConfigurations', ($rootScope, $scope, $timeout, $http, Subjects) ->
