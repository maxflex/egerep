angular
    .module 'Egerep'

    .controller 'PeriodsIndex', ($scope, $timeout, $rootScope, $http, PaymentMethods, DebtTypes, TeacherPaymentTypes, UserService, Confirmed, Account, AccountPayment, Approved, AccountErrors) ->
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true

        $scope.search = {}

        $timeout ->
            load $scope.page
            $scope.current_page = $scope.page

        getPrefix = ->
            prefix = if $scope.type is 'total' then '' else "/#{$scope.type}"

        getCommission = (val) ->
            if val.indexOf('/') isnt -1
                val = val.split('/')[1]
                return if val then parseInt(val) else 0
            else
                return Math.round(parseInt(val) * .25)

        $scope.recalcErrors = ->
            $scope.account_errors_updating = true
            $http.post 'api/command/model-errors', {model: 'accounts'}

        $scope.totalCommission = (account) ->
            total_commission = 0
            $.each account.data, (index, account_data) ->
                $.each account_data, (index, val) ->
                    total_commission += getCommission(val) if val isnt ''
            total_commission

        $scope.pageChanged = ->
            ajaxStart()
            $rootScope.frontend_loading = true
            load $scope.current_page
            paginate 'periods' + getPrefix(), $scope.current_page

        # чтобы можно было использовать модуль пользователей
        $scope.filter = $scope.pageChanged

        $scope.getSum = (payments) ->
            sum = 0
            payments.forEach (payment) -> sum += payment.sum
            sum

        load = (page) ->
            params = getPrefix()
            params += '?page=' + page

            $.each $scope.search, (key, value) ->
                params += "&#{key}=#{value}"
                console.log(key, value)

            $http.get "api/periods#{ params }"
            .then (response) ->
                ajaxEnd()
                $rootScope.frontendStop()
                $scope.data = response.data
                $scope.periods = $scope.data.data

                $timeout ->
                    $('.selectpicker').selectpicker('refresh')
                , 200

        $scope.toggleConfirmed = (period, Resource) ->
            $rootScope.toggleEnumServer period, 'confirmed', Confirmed, Resource
