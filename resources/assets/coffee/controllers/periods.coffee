angular
    .module 'Egerep'

    .controller 'PeriodsIndex', ($scope, $timeout, $rootScope, $http, PaymentMethods, DebtTypes) ->
        bindArguments($scope, arguments)

        $rootScope.frontend_loading = true

        $timeout ->
            load $scope.page
            $scope.current_page = $scope.page

        getCommission = (val) ->
            if val.indexOf('/') isnt -1
                val = val.split('/')[1]
                return if val then parseInt(val) else 0
            else
                return Math.round(parseInt(val) * .25)

        $scope.totalCommission = (account) ->
            total_commission = 0
            $.each account.data, (index, account_data) ->
                $.each account_data, (index, val) ->
                    total_commission += getCommission(val) if val isnt ''
            total_commission

        $scope.pageChanged = ->
            load $scope.current_page
            paginate('periods', $scope.current_page)

        load = (page) ->
            params = '?page=' + page

            $http.get "api/periods#{ params }"
            .then (response) ->
                $rootScope.frontendStop()
                $scope.data = response.data
                $scope.periods = $scope.data.data
