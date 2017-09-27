angular.module('Egerep')
    .controller 'PaymentsIndex', ($scope, $attrs, $timeout, IndexService, Payment, PaymentTypes, UserService) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            $timeout ->
                $('.selectpicker').selectpicker 'refresh'
            , 1000
            $scope.search = if $.cookie("payments") then JSON.parse($.cookie("payments")) else
                addressee_id: ''
                source_id: ''
                expenditure_id: ''
                loan: ''

            $scope.$watchCollection 'search', (newVal, oldVal) ->
                console.log('filter') if IndexService.data_loaded
                $scope.filter() if IndexService.data_loaded
            IndexService.init(Payment, $scope.current_page, $attrs)

        $scope.filter = ->
            $.cookie("payments", JSON.stringify($scope.search), { expires: 365, path: '/' });
            IndexService.current_page = 1
            IndexService.pageChanged()

    .controller 'PaymentForm', ($scope, FormService, Payment, PaymentTypes)->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            FormService.init(Payment, $scope.id, $scope.model)
            FormService.prefix = ''

    .controller 'PaymentAddresseeIndex', ($scope, $attrs, IndexService, PaymentAddressee) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            IndexService.init(PaymentAddressee, $scope.current_page, $attrs)
    .controller 'PaymentAddresseeForm', ($scope, FormService, PaymentAddressee)->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            FormService.init(PaymentAddressee, $scope.id, $scope.model)
            FormService.prefix = 'payments/'

    .controller 'PaymentSourceIndex', ($scope, $attrs, IndexService, PaymentSource) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            IndexService.init(PaymentSource, $scope.current_page, $attrs)
    .controller 'PaymentSourceForm', ($scope, FormService, PaymentSource)->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            FormService.init(PaymentSource, $scope.id, $scope.model)
            FormService.prefix = 'payments/'

    .controller 'PaymentExpenditureIndex', ($scope, $attrs, IndexService, PaymentExpenditure) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            IndexService.init(PaymentExpenditure, $scope.current_page, $attrs)
    .controller 'PaymentExpenditureForm', ($scope, FormService, PaymentExpenditure)->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            FormService.init(PaymentExpenditure, $scope.id, $scope.model)
            FormService.prefix = 'payments/'
