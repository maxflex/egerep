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
                <td style='border: none' id='meeting-info-@{{ account.id }}'>
                    @include('tutors.accounts.partials._meeting_info')
                </td>
            </tr>
        </tbody>
    </table>

    <div class="right-table-scroll" ng-class="{'th-padding': !clients.length}">
        <table class='accounts-table'>
            <thead class='small' ng-repeat-start='account in tutor.last_accounts' ng-if='$index == 0'>
                <tr>
                    @include('tutors.accounts.partials._client_info_cell')
                </tr>
            </thead>
            <tbody ng-repeat-end ng-class="{'disable-events': account.confirmed && !{{ allowed(\Shared\Rights::ER_EDIT_ACCOUNTS, true) }}}">
                <tr ng-repeat='date in getDates($index)' class='tr-@{{ date }}'>
                    <td ng-if='!clients.length' class="fake-cell"></td>
                    <td ng-repeat='client in clients' ng-class="{
                        'attachment-start': date == client.attachment_date,
                        'archive-date': date == client.archive_date,
                        'double-border-bottom': getDay(date) == 6,
                        'no-border-right': clients.length < 10
                    }">
                        <input type="text" class='account-column no-border-outline' id='i-@{{ date }}-@{{ $index }}'
                            ng-focus='selectRow(date)'
                            ng-blur='deselectRow(date)'
                            ng-keyup='periodsCursor(date, $index, $event, account.data[client.attachment_id], date)'
                            ng-class="{
                                'attachment-start': date == client.attachment_date,
                                'archive-date': date == client.archive_date,
                            }"
                            ng-model='account.data[client.attachment_id][date]' title='@{{ formatDate(date) }}'>
                    </td>
                    {{--<td ng-if="clients.length < 10" width="100%" class="no-border-outline"></td>--}}
                </tr>
                <tr>
                    <td ng-if='!clients.length' class="fake-cell"></td>
                </tr>
                <tr>
                    <td ng-if='!clients.length' class="fake-cell"></td>
                    <td ng-repeat='client in clients' class="invisible-td small" style='text-align: center'>
                        @{{ periodLessons(account, client.attachment_id) }}
                    </td>
                </tr>
                <tr>
                    <td class="period-end" id="meeting-info-blank-@{{ account.id }}" ng-style="getStyle(account)">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>