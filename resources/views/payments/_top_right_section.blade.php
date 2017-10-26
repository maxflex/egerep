@section('title-right')
    <a class="pointer" ng-show="selected_payments.length" ng-click="removeSelectedPayments()">удалить (@{{ selected_payments.length }})</a>
    <a class="pointer" onclick="$('#import-button').click()">импорт</a>
    <a href="payments/export">экспорт</a>
    <a href="payments/sources">источники</a>
    <a href="payments/expenditures">статьи</a>
    <a href="payments/remainders">остатки</a>
    <a class="pointer" ng-click="addPaymentDialog()">добавить платеж</a>
@stop
