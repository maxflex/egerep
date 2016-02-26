@extends('app')
@section('title', 'Отчетность')
@section('controller', 'AccountsCtrl')

@section('title-right')
    <span class="link-like link-reverse link-white" ng-click='addAccountDialog()'>добавить расчет</span>
@stop

@section('content')

{{-- FAKE DATES IF NO ACCOUNTS --}}
<div ng-if='tutor.accounts.length == 0'>
    <table class='accounts-table'>
        <thead>
            <tr>
                <td width='75'></td>
                <td ng-repeat='client_id in client_ids' width='50'>
                    клиент @{{ client_id }}
                </td>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat='date in getFakeDates()'>
                <td>@{{ formatDate(date) }}</td>
                <td ng-repeat='client_id in client_ids'>
                    <input type="text" class='account-column no-border-outline' ng-model='account.data[client_id][date]' disabled>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- REAL DATES --}}
<div ng-if='tutor.accounts.length > 0' ng-repeat='account in tutor.accounts'>
    <table class='accounts-table'>
        <thead>
            <tr>
                <td width='75'></td>
                <td ng-repeat='client_id in client_ids' width='50'>
                    клиент @{{ client_id }}
                </td>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat='date in getDates($index)'>
                <td>@{{ formatDate(date) }}</td>
                <td ng-repeat='client_id in client_ids'>
                    <input type="text" class='account-column no-border-outline' ng-model='account.data[client_id][date]'>
                </td>
            </tr>
        </tbody>
    </table>
    <div class='accounts-data'>
        <div class="mbs">
            <span>Итого комиссия за период:</span>
            <input ng-model='account.total_commission' class='no-border-outline' style="width: 50px">
            <ng-pluralize count='account.total_commission' when="{
                'one': 'рубль',
                'few': 'рубля',
                'many': 'рублей',
            }"></ng-pluralize>
        </div>
        <div class="mbs">
            <span>Передано:</span>
            <input ng-model='account.received' class='no-border-outline' style="width: 50px">
            <ng-pluralize count='account.received' when="{
                'one': 'рубль',
                'few': 'рубля',
                'many': 'рублей',
            }"></ng-pluralize>
        </div>
        <div class="mbs">
            <span>Дебет до встречи:</span>
            <input ng-model='account.debt_before' class='no-border-outline' style="width: 50px">
            <ng-pluralize count='account.debt_before' when="{
                'one': 'рубль',
                'few': 'рубля',
                'many': 'рублей',
            }"></ng-pluralize>
        </div>
        <div class="mbs">
            <span>Задолженность:</span>
            <input ng-model='account.debt' class='no-border-outline' style="width: 50px">
            <ng-pluralize count='account.debt' when="{
                'one': 'рубль',
                'few': 'рубля',
                'many': 'рублей',
            }"></ng-pluralize> <span ng-if='account.debt > 0'>(репетитор <span class="link-like"
                    ng-click="toggleEnum(account, 'debt_type', DebtTypes)">@{{ DebtTypes[account.debt_type] }}</span>)</span>
        </div>
        <div class="mbs">
            <span>Метод оплаты:</span>
            <span class="link-like"
                ng-click="toggleEnum(account, 'payment_method', PaymentMethods)">@{{ PaymentMethods[account.payment_method] }}</span>
        </div>
        <div class="mbs">
            <span style="width: auto">Комментарий:</span>
            <input ng-model='account.comment' class='no-border-outline' style="width: 90%">
        </div>
    </div>
</div>


<div class="row" ng-if='tutor.accounts.length > 0'>
    <div class="col-sm-12 center">
        <button class="btn btn-primary" ng-click="save()" ng-disabled="saving">Сохранить</button>
    </div>
</div>


{{-- MODALS --}}
<div class="modal" id='add-account'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Добавление расчета</h4>
            </div>
            <div class="modal-body">
                <input type="text" class="bs-date form-control" placeholder="дата конца периода" ng-model='new_account_date_end'>
            </div>
            <div class="modal-footer center">
                <button type="button" class="btn btn-primary" ng-click="addAccount()">Добавить</button>
            </div>
        </div>
    </div>
</div>
@stop
