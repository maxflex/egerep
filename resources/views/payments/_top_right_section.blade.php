@section('title-right')
    <span style="color: white; margin-right: 25px" ng-show="IndexService.page.total">всего: @{{ IndexService.page.total | number }}</span>
    <a class="pointer" onclick="$('#import-button').click()">импорт</a>
    <a href="payments/export">экспорт</a>
    <a href="payments/sources">источники</a>
    <a href="payments/expenditures">статьи</a>
    <a href="payments/remainders">остатки</a>
    <a class="pointer" ng-click="addPaymentDialog()">добавить платеж</a>
@stop
