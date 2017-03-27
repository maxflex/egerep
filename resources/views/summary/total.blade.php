<div ng-if="type == 'total'">
    <table class="table table-hover summary-table">
        <thead>
            <tr>
                <td width="150">
                </td>
                <td>
                    заявок
                </td>
                <td>
                    стыковок
                </td>
                <td>
                    архиваций
                </td>
                {{-- @rights-refactored --}}
                @if (allowed(\Shared\Rights::ER_SUMMARY_FIELDS))
                <td>
                    получено
                </td>
                <td>
                    прогноз дебета
                </td>
                <td>
                    комиссия
                </td>
                <td>
                    прогноз в неделю
                </td>
                <td>
                    дебет
                </td>
                @endif
                <td>
                    рабочих
                </td>
                <td>
                    новых
                </td>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="(date, summary) in summaries">
                <td>
                    @{{ date | date:'dd MMMM yyyy' }}
                </td>
                <td>
                    @{{ summary.requests.cnt | hideZero }}
                </td>
                <td>
                    @{{ summary.attachments.cnt | hideZero }}
                </td>
                <td>
                    @{{ summary.archives.cnt | hideZero }}
                </td>
                @if (allowed(\Shared\Rights::ER_SUMMARY_FIELDS))
                <td>
                    <span title="@{{ summary.received.sum | number }} + @{{ (summary.mutual_debts.sum ? summary.mutual_debts.sum : 0) | number }}">
                        @{{ +(summary.received.sum) + +(summary.mutual_debts.sum) | hideZero | number:0 }}
                    </span>
                </td>
                <td>
                    @{{ +(summary.total_debts.sum) - +(summary.debts.sum) | hideZero | number:0 }}
                </td>
                <td>
                    @{{ summary.commission.sum | hideZero | number:0 }}
                    <span class='quater-black' ng-show='summary.debts.sum'>
                        <span ng-show='summary.commission.sum'> + </span>
                        @{{ summary.debts.sum | number:0 }}
                    </span>
                </td>
                <td>
                    @{{ summary.forecast.sum | hideZero | number:0 }}
                </td>
                <td>
                    @{{ summary.debt.sum | hideZero | number:0 }}
                </td>
                @endif
                <td>@{{ summary.active_attachments.sum }}</td>
                <td>@{{ summary.new_clients.sum }}</td>
            </tr>
        </tbody>
    </table>
</div>
