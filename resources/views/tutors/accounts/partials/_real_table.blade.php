{{-- REAL DATES --}}
<div ng-if='tutor.last_accounts.length > 0' ng-init='i = 0'>
    <table class='accounts-table'>
        <thead class="high-z-index small">
            <tr>
                <td class='empty-td centered'>
                    <div class='mbs'>&nbsp;</div>
                    <div class='mbs'>&nbsp;</div>
                    <span class='link-like' ng-hide='all_displayed' ng-click='loadPage()'>
                        @{{ left == 1 ? 'все время' : '+1 период'}}
                    </span>
                </td>
            </tr>
        </thead>
        <tbody ng-repeat='account in tutor.last_accounts'>
            <tr ng-repeat='date in getDates($index)' class="tr-@{{ date }}">
                <td class='date-td' ng-class="{'double-border-bottom': getDay(date) == 6}">
                    @{{ formatDate(date) }}
                    <span class="text-gray small" style='margin: 0 5px'>@{{ Weekdays[getDay(date)] }}</span>
                </td>
            </tr>
            <tr>
                <td class='invisible-td small'>в периоде</td>
            </tr>
            <tr>
                <td class="period-end">
                </td>
            </tr>
        </tbody>
    </table>

    <div class="right-table-scroll">
        <table class='accounts-table'>
            <thead class='small' ng-repeat-start='account in tutor.last_accounts' ng-if='$index == 0'>
                <tr>
                    @include('tutors.accounts.partials._client_info_cell')
                </tr>
            </thead>
            <tbody ng-repeat-end>
                <tr ng-repeat='date in getDates($index)' class='tr-@{{ date }}'>
                    <td ng-if='!clients.length' class="fake-cell"></td>
                    <td ng-repeat='client in clients' ng-class="{
                        'attachment-start': date == client.attachment_date,
                        'archive-date': date == client.archive_date,
                        'double-border-bottom': getDay(date) == 6
                    }">
                        <input type="text" class='account-column no-border-outline' id='i-@{{ date }}-@{{ $index }}'
                            ng-focus='selectRow(date)'
                            ng-blur='deselectRow(date)'
                            ng-keyup='periodsCursor(date, $index, $event, account.data[client.id], date)'
                            ng-class="{
                                'attachment-start': date == client.attachment_date,
                                'archive-date': date == client.archive_date,
                            }"
                            ng-model='account.data[client.id][date]' title='@{{ formatDate(date) }}'>
                    </td>
                </tr>
                <tr>
                    <td ng-if='!clients.length' class="fake-cell"></td>
                </tr>
                <tr>
                    <td ng-if='!clients.length' class="fake-cell"></td>
                    <td ng-repeat='client in clients' class="invisible-td small" style='text-align: center'>
                        @{{ periodLessons(account, client.id) }}
                    </td>
                </tr>
                <tr>
                    <td class="period-end" width='100'>
                        <div class='accounts-data' style="position: absolute; margin-top: @{{ clients.length ? '-86' : '80' }}px; width: 1000px">
                            <div class="mbs">
                                <span>Передано (руб.):</span>
                                <pencil-input model='account.received'></pencil-input>
                                <span ng-show='account.received > 0'>
                                    – методом
                                    <span class="link-like" ng-click="toggleEnum(account, 'payment_method', PaymentMethods)">
                                        @{{ PaymentMethods[account.payment_method] }}
                                    </span>
                                </span>
                                <span class='mutual-debt' ng-if="account.mutual_debts">
                                    + @{{ account.mutual_debts.sum }}
                                </span>
                            </div>
                            <div class="mbs">
                                <span>Итого комиссия за период (руб.):</span>
                                @{{ totalCommission(account) }}
                            </div>
                            <div class="mbs">
                                <span>Дебет:</span>
                                <span>@{{ account.debt_calc }}</span>
                            </div>
                            <div class="mbs">
                               <span>Задолженность:</span>
                               <pencil-input model='account.debt'></pencil-input>
                               <span ng-if='account.debt > 0'> – репетитор <span class="link-like-no-color"
                                       ng-class="{
                                           'text-danger': account.debt_type == 0,
                                           'text-success': account.debt_type == 1,
                                       }"
                                       ng-click="toggleEnum(account, 'debt_type', DebtTypes)">@{{ DebtTypes[account.debt_type] }}</span>
                               </span>
                           </div>
                            <div class="mbs">
                                <span>Комментарий:</span>
                                <pencil-input model='account.comment' class="period-comment"></pencil-input>
                                {{-- <div class='period-comment' contenteditable>@{{ account.comment }}</div> --}}
                                {{-- <input ng-model='account.comment' class='no-border-outline' style="width: 90%"> --}}
                            </div>
                            <div class="mbs">
                                <span>Расчет создан:</span>
                                 @{{ account.user_login }} @{{ formatDateTime(account.created_at) }}
                            </div>
                            <div class="mbs">
                                <span>Действия:</span>
                                <span class="link-like margin-right" ng-click="changeDateDialog($index)">изменить дату встречи</span>
                                <span class="link-like text-danger margin-right"  ng-click="remove(account)">удалить встречу</span>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
