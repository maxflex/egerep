angular.module('Egerep')
    .controller 'PaymentsIndex', ($scope, $attrs, $timeout, $http, IndexService, Payment, PaymentTypes, UserService) ->
        bindArguments($scope, arguments)
        $('#import-button').fileupload
            # начало загрузки
            send: ->
                NProgress.configure({ showSpinner: true })
            # во время загрузки
            progress: (e, data) ->
                NProgress.set(data.loaded / data.total)
            # всегда по окончании загрузки (неважно, ошибка или успех)
            always: ->
                NProgress.configure({ showSpinner: false })
                ajaxEnd()
            done: (i, response) ->
                notifySuccess("<b>#{response.result}</b> импортировано")
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

            $scope.$watch 'search.type', (newVal, oldVal) ->
                $scope.filter() if IndexService.data_loaded

            $scope.selected_payments = []
            $scope.tab = 'payments'

            IndexService.init(Payment, $scope.current_page, $attrs)

        $scope.filter = ->
            $.cookie("payments", JSON.stringify($scope.search), { expires: 365, path: '/' });
            IndexService.current_page = 1
            IndexService.pageChanged()

        $scope.keyFilter = (event) ->
            $scope.filter() if event.keyCode is 13

        $scope.selectPayment = (payment) ->
            if payment.id in $scope.selected_payments
                $scope.selected_payments = _.without($scope.selected_payments, payment.id)
            else
                $scope.selected_payments.push(payment.id)

        $scope.removeSelectedPayments = ->
            ajaxStart()
            $.post('api/payments/delete', {ids: $scope.selected_payments}).then (response) ->
                $scope.selected_payments = []
                $scope.filter()
                ajaxEnd()

        $scope.addPaymentDialog = (payment = false) ->
            $scope.modal_payment = _.clone(payment || $scope.fresh_payment)
            $('#payment-stream-modal').modal('show')

        $scope.savePayment = ->
            $scope.adding_payment = true
            func = if $scope.modal_payment.id then Payment.update else Payment.save
            func $scope.modal_payment, (response) ->
                $scope.adding_payment = false
                $('#payment-stream-modal').modal('hide')
                $scope.filter()

        $scope.clonePayment = ->
            new_payment = _.clone($scope.modal_payment)
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
            $scope.modal_payment = _.clone(model)
            $('#payment-stream-modal').modal('show')

        $scope.formatStatDate = (date) ->
            moment(date + '-01').format('MMMM')

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

        getTotal = ->
            total = {in: 0, out: 0, sum: 0}
            $.each $scope.stats_data, (year, data) ->
                data.forEach (d) ->
                    total.in  += parseFloat(d.in)
                    total.out += parseFloat(d.out)
                    total.sum += parseFloat(d.sum)
            total

    .controller 'PaymentForm', ($scope, FormService, Payment, PaymentTypes)->
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

    .controller 'PaymentSourceForm', ($scope, FormService, PaymentSource)->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            FormService.init(PaymentSource, $scope.id, $scope.model)
            FormService.prefix = 'payments/'

    .controller 'PaymentExpenditureIndex', ($scope, $attrs, $timeout, IndexService, PaymentExpenditure) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            IndexService.init(PaymentExpenditure, $scope.current_page, $attrs)

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
                        PaymentExpenditure.update({id: model.id, position: index})

    .controller 'PaymentExpenditureForm', ($scope, FormService, PaymentExpenditure)->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            FormService.init(PaymentExpenditure, $scope.id, $scope.model)
            FormService.prefix = 'payments/'

    .controller 'PaymentRemainders', ($scope, $http, $timeout) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            $timeout ->
                load($scope.page)
                $scope.current_page = $scope.page

        $scope.pageChanged = ->
            load($scope.current_page)
            paginate 'payments/remainders', $scope.current_page

        load = (page) ->
            ajaxStart()
            $http.post 'api/payments/remainders',
                page: page
            .then (response) ->
                ajaxEnd()
                $scope.data = response.data
