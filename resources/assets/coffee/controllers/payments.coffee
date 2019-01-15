angular.module('Egerep')
    .controller 'PaymentsIndex', ($scope, $attrs, $timeout, $http, IndexService, Payment, UserService, Checked) ->
        bindArguments($scope, arguments)

        $scope.payment_actions_index = null

        $('#import-button').fileupload
            # начало загрузки
            # send: ->
            #     NProgress.configure({ showSpinner: true })
            # # во время загрузки
            # progress: (e, data) ->
            #     NProgress.set(data.loaded / data.total)
            # # всегда по окончании загрузки (неважно, ошибка или успех)
            start: -> ajaxStart()
            always: -> ajaxEnd()
            done: (i, response) ->
                notifySuccess("<b>#{response.result}</b> импортировано")
                $scope.filter()

            error: (response) ->
                console.log(response)
                notifyError(response.responseJSON)

        angular.element(document).ready ->
            $timeout ->
                $('.selectpicker').selectpicker 'refresh'
            , 1000
            $scope.search = if $.cookie("payments") then JSON.parse($.cookie("payments")) else
                addressee_id: ''
                source_id: ''
                expenditure_id: ''
                type: ''

            $scope.tab = 'payments'

            IndexService.init(Payment, $scope.current_page, $attrs)

        $scope.filter = ->
            $.cookie("payments", JSON.stringify($scope.search), { expires: 365, path: '/' });
            IndexService.current_page = 1
            IndexService.pageChanged()

        $scope.keyFilter = (event) ->
            $scope.filter() if event.keyCode is 13
        
        $scope.selectAllExpenditures = ->
            except = [40, 42, 29]
            $scope.search_stats.expenditure_ids = []
            $scope.expenditures.forEach (expenditure) ->
                expenditure.data.forEach (d) ->
                    $scope.search_stats.expenditure_ids.push(d.id.toString()) if except.indexOf(d.id) is -1
            $timeout -> $('.selectpicker').selectpicker('refresh')
        
        $scope.selectAllSources = ->
            except = [4, 6]
            $scope.search_stats.wallet_ids = []
            $scope.sources.forEach (source) ->
                $scope.search_stats.wallet_ids.push(source.id.toString()) if except.indexOf(source.id) is -1
            $timeout -> $('.selectpicker').selectpicker('refresh')

        
        $scope.setPaymentActionsIndex = (index) ->
            $scope.payment_actions_index = index

        $scope.getExpenditure = (id) ->
            id = parseInt(id)
            expenditure = null
            $scope.expenditures.forEach (e) ->
                return if expenditure
                e.data.forEach (d) ->
                    if d.id == id
                        expenditure = d
                        return
            expenditure

        $scope.addPaymentDialog = (payment = false) ->
            $scope.modal_payment = _.clone(payment || $scope.fresh_payment)
            $('#payment-stream-modal').modal('show')

        $scope.savePayment = ->
            $scope.adding_payment = true

            if not $scope.modal_payment.date
                $('#payment-date').focus()
                notifyError("укажите дату")
                return

            if not $scope.modal_payment.source_id
                notifyError("укажите источник")
                return

            if not $scope.modal_payment.addressee_id
                notifyError("укажите адресат")
                return

            if not $scope.modal_payment.expenditure_id
                notifyError("укажите статью")
                return

            if not $scope.modal_payment.purpose
                $('#payment-purpose').focus()
                notifyError("укажите назначение")
                return

            func = if $scope.modal_payment.id then Payment.update else Payment.save
            func $scope.modal_payment, (response) ->
                $scope.adding_payment = false
                $('#payment-stream-modal').modal('hide')
                $scope.filter()

        $scope.clonePayment = (payment) ->
            $scope.payment_actions_index = null
            new_payment = _.clone(payment)
            delete new_payment.id
            delete new_payment.created_at
            delete new_payment.updated_at
            delete new_payment.user_id
            $scope.addPaymentDialog(new_payment)

        $scope.deletePayment = ->
            Payment.delete {id: $scope.modal_payment.id}, (response) ->
                $('#payment-stream-modal').modal('hide')
                $scope.filter()

        $scope.editPayment = (model) ->
            $scope.payment_actions_index = null
            $scope.modal_payment = _.clone(model)
            $('#payment-stream-modal').modal('show')

        $scope.formatStatDate = (date) ->
            moment(date + '-01').format('MM.YYYY')

        $scope.search_stats =
            mode: 'by_days'
            # date_start: '01.01.2017'
            date_start: ''
            date_end: ''
        
        $scope.$watch 'search_stats.mode', (newVal, oldVal) ->
            if newVal isnt oldVal
                # выбраны какие-то фильтры
                if Object.keys($scope.search_stats).length > 3
                    $scope.loadStats()

        $scope.loadStats = ->
            return if $scope.tab isnt 'stats'
            $scope.stats_loading = true
            ajaxStart()
            $http.post 'api/payments/stats', $scope.search_stats
            .then (response) ->
                ajaxEnd()
                $scope.stats_loading = false
                if response.data
                    $scope.stats_data = response.data.data
                    $scope.expenditure_data = response.data.expenditures
                    $timeout -> $scope.totals = getTotal()
                else
                    $scope.stats_data = null

        $scope.formatDecimal = (a) ->
            return if not a
            a = a.toString()
            return a if a.indexOf('.') is -1
            parts = a.split('.')
            parts[1] = parts[1].substr(0, 2)
            return parts.join(',')

        getTotal = ->
            total = {in: 0, out: 0, sum: 0}
            $.each $scope.stats_data, (index, d) ->
                total.in  += parseFloat(d.in)
                total.out += parseFloat(d.out)
                total.sum += parseFloat(d.sum)
            total
        
        # разница – в прошлом году этого месяца
        $scope.lastYearDiff = (data) ->
            date_parts = data.date.split('-')
            last_year_data = $scope.stats_data.find (e) -> e.date is ((parseInt(date_parts[0]) - 1) + '-' + date_parts[1])
            if last_year_data isnt undefined
                return (data.sum - last_year_data.sum)
            return ''
        
        $scope.diff2 = (index) ->
            s1 = 0
            i = 0
            while i <= 11
                d = $scope.stats_data[index + i]
                if d is undefined
                    # console.log('qutting 1 because ' + (index + i) + ' doesnt exist')
                    return ''
                s1 += parseInt(d.sum)
                i++
            s2 = 0
            i = 1
            while i <= 12
                d = $scope.stats_data[index + i]
                if d is undefined
                    # console.log('qutting 2 because ' + (index + i) + ' doesnt exist')
                    return ''
                s2 += parseInt(d.sum)
                i++
            return s1 - s2



    .controller 'PaymentForm', ($scope, FormService, Payment)->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            FormService.init(Payment, $scope.id, $scope.model)
            FormService.prefix = ''

    .controller 'PaymentSourceIndex', ($scope, $attrs, $timeout, IndexService, PaymentSource) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            IndexService.init(PaymentSource, $scope.current_page, $attrs)

        $scope.sortableOptions =
            cursor: "move"
            opacity: 0.9,
            zIndex: 9999
            tolerance: "pointer"
            axis: 'y'
            containment: "parent"
            update: (event, ui) ->
                $timeout ->
                    IndexService.page.data.forEach (model, index) ->
                        PaymentSource.update({id: model.id, position: index})

    .controller 'PaymentSourceForm', ($scope, FormService, PaymentSource, SourceRemainder)->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            FormService.init(PaymentSource, $scope.id, $scope.model)
            FormService.prefix = 'payments/'

        $scope.editRemainder = (model) ->
            $scope.modal_remainder = _.clone(model)
            $('#remainder-stream-modal').modal('show')

        $scope.deleteRemainder = (model) ->
            SourceRemainder.delete {id: model.id}, (response) ->
                $('#remainder-stream-modal').modal('hide')
                FormService.init(PaymentSource, $scope.id, $scope.model)

        $scope.addRemainderDialog = (remainder = false) ->
            $scope.modal_remainder = _.clone(remainder || {date: moment().format('DD.MM.YYYY'), source_id: $scope.id})
            $('#remainder-stream-modal').modal('show')

        $scope.saveRemainder = ->
            $scope.adding_remainder = true

            func = if $scope.modal_remainder.id then SourceRemainder.update else SourceRemainder.save
            func $scope.modal_remainder, (response) ->
                $scope.adding_remainder = false
                $('#remainder-stream-modal').modal('hide')
                FormService.init(PaymentSource, $scope.id, $scope.model)

    .controller 'PaymentExpenditureIndex', ($scope, $attrs, $timeout, IndexService, PaymentExpenditure, PaymentExpenditureGroup) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            $scope.groups = PaymentExpenditureGroup.query()

        $scope.onEdit = (id, event) ->
            PaymentExpenditureGroup.update {id: id, name: $(event.target).text()}

        $scope.removeGroup = (group) ->
            bootbox.confirm "Вы уверены, что хотите удалить группу «#{group.name}»", (response) ->
                if response is true
                    PaymentExpenditureGroup.remove {id: group.id}, -> $scope.groups = PaymentExpenditureGroup.query()


        $scope.sortableOptions =
            cursor: "move"
            opacity: 0.9,
            zIndex: 9999
            tolerance: "pointer"
            axis: 'y'
            containment: "parent"
            update: (event, ui, data) ->
                $timeout ->
                    $scope.groups.forEach (group) ->
                        group.data.forEach (model, index) ->
                            PaymentExpenditure.update({id: model.id, position: index})

        $scope.sortableGroupOptions =
            cursor: "move"
            opacity: 0.9,
            zIndex: 9999
            tolerance: "pointer"
            axis: 'y'
            containment: "parent"
            items: ".item-draggable"
            update: (event, ui, data) ->
                $timeout ->
                    $scope.groups.forEach (group, index) ->
                        PaymentExpenditureGroup.update({id: group.id, position: index})

    .controller 'PaymentExpenditureForm', ($scope, $timeout, FormService, PaymentExpenditure, PaymentExpenditureGroup)->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            FormService.init(PaymentExpenditure, $scope.id, $scope.model)
            FormService.prefix = 'payments/'

        $scope.changeGroup = ->
            if FormService.model.group_id is -1
                FormService.model.group_id = ''
                $('#new-group').modal('show')
            # console.log(FormService.model.group_id)

        $scope.createNewGroup = ->
            $('#new-group').modal('hide')
            PaymentExpenditureGroup.save
                name: $scope.new_group_name
            , (response) ->
                $scope.new_group_name = ''
                $scope.groups.push(response)
                FormService.model.group_id = response.id
                $timeout -> $('.selectpicker').selectpicker('refresh')

    .controller 'PaymentRemainders', ($scope, $http, $timeout) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            # $timeout ->
            #     # load($scope.page)
            #     $scope.current_page = $scope.page

        $scope.filterChanged = ->
            # load($scope.page)
            $scope.current_page = 1
            load(1)

        $scope.pageChanged = ->
            load($scope.current_page)
            paginate 'payments/remainders', $scope.current_page

        $scope.getDatesReversed = ->
            return if not $scope.data
            arr = []
            $.each $scope.data.items, (date) ->
                arr.push(date)
            arr.sort().reverse()

        load = (page) ->
            ajaxStart()
            $http.post 'api/payments/remainders',
                page: page
                source_id: $scope.source_id
            .then (response) ->
                ajaxEnd()
                $scope.data = response.data
