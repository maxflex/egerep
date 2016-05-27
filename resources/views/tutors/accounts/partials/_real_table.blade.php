{{-- REAL DATES --}}
<div ng-if='tutor.last_accounts.length > 0' ng-init='i = 0'>
    <table class='accounts-table'>
        <thead class="high-z-index">
            <tr>
                <td class='empty-td'>
                    <span class='link-like' ng-hide='all_displayed' ng-click='loadPage()'>
                        +1 период
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
                    <td ng-repeat='client in clients' width='77' class="client-draggable" data-id='@{{ client.id }}'>
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
                    }">
                        <input type="text" class='account-column no-border-outline' id='i-@{{ $parent.$index }}-@{{ $index }}'
                            ng-focus='selectRow(date)'
                            ng-keyup='periodsCursor($parent.$index, $index, $event, date)'
                            ng-class="{
                                'attachment-start': date == client.attachment_date,
                                'archive-date': date == client.archive_date,
                            }"
                            ng-model='account.data[client.id][date]' title='@{{ formatDate(date) }}'>
                    </td>
                </tr>
                <tr>
                    <td class="period-end" width='77'>
                        <div class='accounts-data' style="position: absolute; margin-top: -86px; width: 1000px">
                            <div class="mbs">
                                <span>Передано (руб.):</span>
                                <pencil-input model='account.received'></pencil-input>
                                <span ng-show='account.received > 0'>
                                    – методом
                                    <span class="link-like" ng-click="toggleEnum(account, 'payment_method', PaymentMethods)">
                                        @{{ PaymentMethods[account.payment_method] }}
                                    </span>
                                </span>
                            </div>
                            <div class="mbs">
                                <span>Итого комиссия за период (руб.):</span>
                                @{{ totalCommission(account) }}
                            </div>
                            <div class="mbs">
                                <span>Дебет до встречи (руб.):</span>
                                <pencil-input model='account.debt_before'></pencil-input>
                            </div>
                            <div class="mbs">
                                <span>Задолженность (руб.):</span>
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
