{{-- REAL DATES --}}
<div ng-if='tutor.last_accounts.length > 0'>

    <div class='inline-block'>
        <table class='accounts-table'>
            <thead>
                <tr>
                    <td class='empty-td'></td>
                </tr>
            </thead>
            <tbody ng-repeat='account in tutor.last_accounts'>
                <tr ng-repeat='date in getDates($index)'>
                    <td class='date-td'>@{{ formatDate(date) }}</td>
                </tr>
                <tr>
                    <td class="period-end">
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="right-table-scroll">
            <table class='accounts-table'>
                <thead ng-repeat-start='account in tutor.last_accounts' ng-if='$index == 0'>
                    <tr>
                        <td ng-repeat='client in client_ids' width='107'>
                            <a href='@{{ client.link }}'>клиент @{{ client.id }}</a>
                        </td>
                    </tr>
                </thead>
                <tbody ng-repeat-end>
                    <tr ng-repeat='date in getDates($index)'>
                        <td ng-repeat='client in client_ids'>
                            <input type="text" class='account-column no-border-outline' ng-model='account.data[client.id][date]' title='@{{ formatDate(date) }}'>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="1000" class="period-end">
                            <div class='accounts-data'>
                                <div class="mbs">
                                    <span>Итого комиссия за период:</span>
                                    <input ng-model='account.total_commission' readonly class='no-border-outline' style="width: 50px">
                                    <ng-pluralize count='account.total_commission' when="{
                                        'one': 'рубль',
                                        'few': 'рубля',
                                        'many': 'рублей',
                                    }"></ng-pluralize> <span class="link-like show-on-hover" ng-click="changeDateDialog($index)">изменить дату встречи</span>
                                    <span class="link-like text-danger show-on-hover"  ng-click="remove(account)">удалить встречу</span>
                                </div>
                                <div class="mbs">
                                    <span>Передано:</span>
                                    <input ng-model='account.received' class='no-border-outline' style="width: 50px">
                                    <ng-pluralize count='account.received' when="{
                                        'one': 'рубль',
                                        'few': 'рубля',
                                        'many': 'рублей',
                                    }"></ng-pluralize> методом <span class="link-like"
                                        ng-click="toggleEnum(account, 'payment_method', PaymentMethods)">@{{ PaymentMethods[account.payment_method] }}</span>
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
                                    <span style="width: auto">Комментарий:</span>
                                    <input ng-model='account.comment' class='no-border-outline' style="width: 90%">
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
