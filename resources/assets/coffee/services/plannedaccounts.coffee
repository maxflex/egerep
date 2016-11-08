angular.module 'Egerep'
    .service 'PlannedAccountService', ($rootScope, $timeout, PlannedAccount)->
        this.has_planned_accounts = 0;

        this.showDialog = ->
            this.date = ''

            $('#pa-date').datepicker('destroy')
            $('#pa-date').datepicker
                language	: 'ru'
#                startDate   : getCalendarStartDate
                autoclose	: true
                orientation	: 'bottom auto'

            $('#add-planned-account').modal 'show'
            $timeout ->
                $('#planned-account').selectpicker('refresh')
            return

        this.add = (planned_account) ->
            PlannedAccount.save planned_account, (response)->
                console.log response

        this.update = (planned_account) ->
#            if $rootScope.account_is_planned
                PlannedAccount.update
                    id: planned_account.id
                    data: planned_account
#            else
#                PlannedAccount.delete
#                    id: planned_account.id
        this