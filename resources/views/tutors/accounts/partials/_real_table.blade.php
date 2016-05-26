{{-- REAL DATES --}}
<div ng-if='tutor.last_accounts.length > 0'>
    <table class='accounts-table'>
        <thead class="high-z-index">
            <tr>
                <td class='empty-td'>
                    <span class='link-like' ng-click='loadPage()' ng-hide='current_period == 4'>
                        <span ng-show='current_period == 1'>+1 месяц</span>
                        <span ng-show='current_period == 2'>+1 год</span>
                        <span ng-show='current_period == 3'>всё время</span>
                    </span>
                </td>
            </tr>
        </thead>
        <tbody ng-repeat='account in tutor.last_accounts'>
            <tr ng-repeat='date in getDates($index)' class="tr-@{{ date }}">
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
                    <td ng-repeat='client in clients' width='77'>
                        <a href='@{{ client.link }}'>@{{ client.name | cut:false:10 }}</a>
                        <br>
                        <span class='text-gray'>
                            <span ng-show='client.grade'>@{{ Grades[client.grade] }}</span>
                            <span ng-hide='client.grade'>класс не указан</span>
                        </span>

                    </td>
                </tr>
            </thead>
            <tbody ng-repeat-end>
                <tr ng-repeat='date in getDates($index)' class='tr-@{{ date }}'>
                    <td ng-repeat='client in clients' ng-class="{
                        'attachment-start': date == client.attachment_date,
                        'archive-date': date == client.archive_date,
                    }" id='td-@{{ $parent.$index }}-@{{ $index }}'>
                        <input type="text" class='account-column no-border-outline' id='i-@{{ $parent.$index }}-@{{ $index }}'
                            ng-focus='selectRow(date)'
                            ng-keyup='periodsCursor($parent.$index, $index, $event)'
                            ng-class="{
                                'attachment-start': date == client.attachment_date,
                                'archive-date': date == client.archive_date,
                            }"
                            ng-model='account.data[client.id][date]' title='@{{ formatDate(date) }}'>
                    </td>
                </tr>
                <tr>
                    <td class="period-end" width='77'>
                        <div class='accounts-data' style="position: absolute; margin-top: -57px">
                            <div class="mbs">
                                <span>Итого комиссия за период:</span>
                                <input ng-model='account.total_commission' readonly class='no-border-outline' style="width: 60px">
                                <ng-pluralize count='account.total_commission' when="{
                                    'one': 'рубль',
                                    'few': 'рубля',
                                    'many': 'рублей',
                                }"></ng-pluralize> <span class="link-like show-on-hover" ng-click="changeDateDialog($index)">изменить дату встречи</span>
                                <span class="link-like text-danger show-on-hover"  ng-click="remove(account)">удалить встречу</span>
                            </div>
                            <div class="mbs">
                                <span>Передано:</span>
                                <input ng-model='account.received' class='no-border-outline' style="width: 60px">
                                <ng-pluralize count='account.received' when="{
                                    'one': 'рубль',
                                    'few': 'рубля',
                                    'many': 'рублей',
                                }"></ng-pluralize> методом <span class="link-like"
                                    ng-click="toggleEnum(account, 'payment_method', PaymentMethods)">@{{ PaymentMethods[account.payment_method] }}</span>
                            </div>
                            <div class="mbs">
                                <span>Дебет до встречи:</span>
                                <input ng-model='account.debt_before' class='no-border-outline' style="width: 60px">
                                <ng-pluralize count='account.debt_before' when="{
                                    'one': 'рубль',
                                    'few': 'рубля',
                                    'many': 'рублей',
                                }"></ng-pluralize>
                            </div>
                            <div class="mbs">
                                <span>Задолженность:</span>
                                <input ng-model='account.debt' class='no-border-outline' style="width: 60px">
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
                            <div class="mbs">
                                 @{{ account.user_login }} @{{ formatDateTime(account.created_at) }}
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
