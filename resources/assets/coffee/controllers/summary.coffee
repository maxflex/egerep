angular
    .module 'Egerep'
    .controller 'SummaryUsers', ($scope, $rootScope, $timeout, $http, UserService, RequestStates, AttachmentService) ->
        bindArguments($scope, arguments)

        $timeout ->
            $scope.search = {}
            # $scope.search = {date_from: '01.04.2017', date_to: '30.04.2017'}
            $scope.search.user_ids = [$scope.user.id.toString()] if not $scope.allowed_all
            $scope.search.type = 'months' if not $scope.search.type
            # for debug $scope.search.date_from = '01.05.2016'
            # for debug $scope.search.date_to = '31.07.2016'
            $timeout -> $('#change-user, #change-type').selectpicker 'refresh'
        , 500

        $scope.updateEfficency = ->
            $scope.efficency_updating = true
            $http.post 'api/command/recalc-efficency'

        $scope.update = ->
            $rootScope.frontend_loading = true
            $http.post 'api/summary/users', $scope.search
            .then (response) ->
                $rootScope.frontend_loading = false
                $scope.stats = response.data
            , (error) ->
                $rootScope.frontend_loading = false
                $scope.stats = null

        $scope.getExplanation = ->
            $rootScope.explaination_loading = true
            $http.post 'api/summary/users/explain', $scope.search
            .then (response) ->
                $rootScope.explaination_loading = false
                $scope.stats.efficency = response.data

        # расшифровка по преподавателям
        $scope.explaination_tutors_loading = false

        $scope.getExplanationByTutors = ->
            $scope.explaination_tutors_loading = true
            $http.post 'api/summary/users/explain/tutors', $scope.search
            .then (response) ->
                $scope.explaination_tutors_loading = false
                $scope.explanation_tutors = response.data


        $scope.monthYear = (date) ->
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

        $scope.updateDebt = ->
            $scope.debt_updating = true
            $http.post 'api/command/recalc-debt'
                .then (response) ->
                    $scope.debt_updating = false
                    $scope.debt_updated  = response.data.debt_updated
                    $scope.debt_sum      = response.data.debt_sum

        $timeout ->
            loadSummary $scope.page
            $scope.current_page = $scope.page

        getPrefix = ->
            prefix = if $scope.type is 'total' then '' else "/#{$scope.type}"

        $scope.pageChanged = ->
            ajaxStart()
            loadSummary $scope.current_page
            paginate 'summary' + getPrefix() + '/' + $scope.filter, $scope.current_page

        $scope.updateDebt = ->
            $scope.debt_updating = 1
            $http.post 'api/command/recalc-debt'

        loadSummary = (page) ->
            params  = getPrefix()
            params += '?page='   + page
            params += '&filter=' + $scope.filter
            $http.post "api/summary#{ params }"
            .then (response) ->
                ajaxEnd()
                $rootScope.frontendStop()
                $scope.summaries = response.data
