angular.module 'Egerep'
    .service 'PlannedAccountService', ($rootScope, $timeout, PlannedAccount) ->
        this.has_planned_accounts = 0;

        this.showDialog = ->
            this.date = ''

            $('#pa-date').datepicker('destroy')
            $('#pa-date').datepicker
                language	: 'ru'
                autoclose	: true
                orientation	: 'bottom auto'

            $('#add-planned-account').modal 'show'
            this.refresh()
            return

        this.add = (planned_account, tutor_id) ->
            planned_account['tutor_id'] = tutor_id
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
                $('#add-planned-account').modal 'hide'
                return
        this.refresh = ->
            $timeout ->
                $('#add-planned-account .selectpicker option').each (index, el) ->
                    $(el).data 'subtext', $(el).attr 'data-subtext'
                    $(el).data 'content', $(el).attr 'data-content'

                $('#add-planned-account .selectpicker').selectpicker 'refresh'
            , 200
        this