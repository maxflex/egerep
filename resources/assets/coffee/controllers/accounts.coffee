angular.module('Egerep')
    .controller 'AccountsCtrl', ($scope, Account, PaymentMethods, DebtTypes) ->
        $scope.PaymentMethods = PaymentMethods
        $scope.DebtTypes = DebtTypes
        $scope.current_scope = $scope

        $scope.save = ->
            $.each $scope.tutor.accounts, (index, account) ->
                Account.update account

        $scope.getFakeDates = ->
            dates = []
            current_date = moment().subtract(10, 'days').format('YYYY-MM-DD')
            while current_date <= moment().format('YYYY-MM-DD')
                dates.push current_date
                current_date = moment(current_date).add(1, 'days').format('YYYY-MM-DD')
            dates


        $scope.getDates = (index) ->
            dates = []
            # если нулевой элемент, то отсчитываем от даты первой стыковки (самой ранней стыковки)
            # иначе отсчитываем от даты конца предыдущего периода
            if not index
                current_date = moment($scope.first_attachment_date).format('YYYY-MM-DD')
            else
                current_date = moment($scope.tutor.accounts[index - 1].date_end).add(1, 'days').format('YYYY-MM-DD')

            while (current_date <= $scope.tutor.accounts[index].date_end)
                dates.push current_date
                current_date = moment(current_date).add(1, 'days').format('YYYY-MM-DD')
            dates

        $scope.addAccountDialog = ->
            $scope.dialog 'add-account'

        $scope.addAccount = ->
            Account.save
                date_end: convertDate($scope.new_account_date_end)
                tutor_id: $scope.tutor.id
            , (new_account) ->
                $scope.tutor.accounts.push(new_account)
                $scope.closeDialog 'add-account'
