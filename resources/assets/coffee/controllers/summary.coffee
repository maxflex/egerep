angular
    .module 'Egerep'
    .controller 'SummaryUsers', ($scope, $rootScope, $timeout, $http, UserService, RequestStates, AttachmentService) ->
        bindArguments($scope, arguments)

        $timeout ->
            $scope.search = {}
            $scope.search.user_ids = [$scope.user.id.toString()] if not $scope.allowed_all
            $scope.search.type = 'months' if not $scope.search.type
            # for debug $scope.search.date_from = '01.05.2016'
            # for debug $scope.search.date_to = '31.07.2016'
            $timeout -> $('#change-user, #change-type').selectpicker 'refresh'
        , 500

        $scope.update = ->
            $rootScope.frontend_loading = true
            $http.post 'api/summary/users', $scope.search
            .then (response) ->
                $rootScope.frontend_loading = false
                $scope.stats = response.data

        $scope.getExplanation = ->
            $rootScope.explaination_loading = true
            $http.post 'api/summary/users/explain', $scope.search
            .then (response) ->
                $rootScope.explaination_loading = false
                $scope.stats.efficency = response.data

        $scope.monthYear = (date) ->
            date = date.split(".")
            date = date.reverse()
            date = date.join("-")
            moment(date).format('MMMM YYYY')

        $scope.sumEfficency = ->
            sum = _.reduce $scope.stats.efficency, (sum, request) ->
                _.each request.attachments, (attachment) ->
                    sum += attachment.rate
                sum
            , 0

            sum.toFixed(2)

        $scope.sumShare = ->
            sum = _.reduce $scope.stats.efficency, (sum, request) ->
                if request.attachments.length
                    _.each request.attachments, (attachment) ->
                        sum += attachment.share
                sum += 1 if $scope.isDenied(request)
                sum
            , 0

            sum.toFixed(2)

        $scope.isDenied = (request) ->
            request.state in ['deny']

    .controller 'SummaryIndex', ($rootScope, $scope, $http, $timeout, PaymentMethods) ->
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true
        $scope.debt_updating = false

        $scope.getSum = (summary) ->
            (parseInt(summary.sum) or 0) + (parseInt(summary.debt_sum) or 0)

        $scope.updateDebt = ->
            $scope.debt_updating = true
            $http.post 'api/command/recalc-debt'
                .then (response) ->
                    $scope.debt_updating = false
                    $scope.debt_updated = response.data.debt_updated
                    $scope.total_debt   = response.data.total_debt

        $timeout ->
            loadSummary $scope.page
            $scope.current_page = $scope.page

        getPrefix = ->
            prefix = if $scope.type is 'total' then '' else "/#{$scope.type}"

        $scope.pageChanged = ->
            ajaxStart()
            loadSummary $scope.current_page
            paginate 'summary' + getPrefix() + '/' + $scope.filter, $scope.current_page

        loadSummary = (page) ->
            params  = getPrefix()
            params += '?page='   + page
            params += '&filter=' + $scope.filter
            $http.post "api/summary#{ params }"
            .then (response) ->
                ajaxEnd()
                $rootScope.frontendStop()
                $scope.summaries = response.data
