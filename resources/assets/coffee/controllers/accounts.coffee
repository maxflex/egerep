angular.module('Egerep')
    .controller 'AccountsCtrl', ($scope, Account, PaymentMethods, DebtTypes) ->
        $scope.PaymentMethods = PaymentMethods
        $scope.DebtTypes = DebtTypes
        $scope.current_scope = $scope

        getAccountStartDate = (index) ->
            if index > 0
                moment($scope.tutor.accounts[index - 1].date_end).add(1, 'days').toDate()
            else
                new Date $scope.first_attachment_date

        getAccountEndDate = (index) ->
            if (index + 1) is $scope.tutor.accounts.length
                ''
            else
                moment($scope.tutor.accounts[index + 1].date_end).subtract(1, 'days').toDate()

        $scope.changeDateDialog = (index) ->
            $('#date-end-change').datepicker('destroy')
            $('#date-end-change').datepicker
                language	: 'ru'
                autoclose	: true
                orientation	: 'bottom auto'
                startDate   : getAccountStartDate(index)
                endDate     : getAccountEndDate(index)

            $scope.selected_account = $scope.tutor.accounts[index]
            $scope.change_date_end = $scope.formatDate($scope.selected_account.date_end, true)
            $scope.dialog 'change-account-date'

        $scope.changeDate = ->
            $scope.selected_account.date_end = convertDate($scope.change_date_end)
            Account.update
                id: $scope.selected_account.id
                date_end: $scope.selected_account.date_end
            # , (response) ->
            #     $scope.selected_account = response
            $scope.closeDialog 'change-account-date'

        $scope.remove = (account) ->
            bootbox.confirm 'Удалить встречу?', (result) ->
                if result is true
                    Account.delete {id: account.id}, ->
                        $scope.tutor.accounts = removeById($scope.tutor.accounts, account.id)


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

        # откуда можно выбирать дату в календаре
        getCalendarStartDate = ->
            if $scope.tutor.accounts.length > 0
                date_end = $scope.tutor.accounts[$scope.tutor.accounts.length - 1].date_end
                moment(date_end).add(1, 'days').toDate()
            else
                new Date $scope.first_attachment_date

        $scope.addAccountDialog = ->
            $scope.new_account_date_end = ''
            # @todo: узнать, как делается refresh
            $('#date-end').datepicker('destroy')
            $('#date-end').datepicker
                language	: 'ru'
                startDate   : getCalendarStartDate()
                autoclose	: true
                orientation	: 'bottom auto'

            $scope.dialog 'add-account'

        $scope.addAccount = ->
            Account.save
                date_end: convertDate($scope.new_account_date_end)
                tutor_id: $scope.tutor.id
            , (new_account) ->
                $scope.tutor.accounts.push(new_account)
                $scope.closeDialog 'add-account'
