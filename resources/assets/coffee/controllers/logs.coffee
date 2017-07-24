angular
    .module 'Egerep'
    .controller 'LogsIndex', ($rootScope, $scope, $timeout, $http, UserService, LogTypes) ->
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true

        $scope.$watch 'search.table', (newVal, oldVal) ->
            $scope.search.column = null if ((newVal && oldVal) || (oldVal && not newVal))

        $scope.toJson = (data)->
            JSON.parse(data)

        $scope.refreshCounts = ->
            $timeout ->
                $('.selectpicker option').each (index, el) ->
                    $(el).data 'subtext', $(el).attr 'data-subtext'
                    $(el).data 'content', $(el).attr 'data-content'
                $('.selectpicker').selectpicker 'refresh'
            , 100

        $scope.filter = ->
            $.cookie("logs", JSON.stringify($scope.search), { expires: 365, path: '/' });
            $scope.current_page = 1
            $scope.pageChanged()

        $scope.keyFilter = (event) ->
            $scope.filter() if event.keyCode is 13

        $timeout ->
            $scope.search = if $.cookie("logs") then JSON.parse($.cookie("logs")) else {}
            load $scope.page
            $scope.current_page = $scope.page

        $scope.pageChanged = ->
            $rootScope.frontend_loading = true
            load $scope.current_page
            paginate('logs', $scope.current_page)


        load = (page) ->
            params = '?page=' + page

            $http.get "api/logs#{ params }"
            .then (response) ->
                console.log response
                $scope.counts = response.data.counts
                $scope.data = response.data.data
                $scope.logs = response.data.data.data
                $rootScope.frontend_loading = false
                $scope.refreshCounts()
