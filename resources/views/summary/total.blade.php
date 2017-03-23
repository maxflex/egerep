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
                    комиссия
                </td>
                <td>
                    прогноз
                </td>
                <td>
                    дебет
                </td>
                @endif
                <td>
                    рабочих процессов
                </td>
                <td>
                    новых процессов
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
                        @{{ +(summary.received.sum) + +(summary.mutual_debts.sum) | hideZero | number }}
                    </span>
                </td>
                <td>
                    @{{ summary.commission.sum | hideZero | number }}
                    <span class='mutual-debt' ng-show='summary.debts.sum'>
                        <span ng-show='summary.commission.sum'> + </span>
                        @{{ summary.debts.sum | number }}
                    </span>
                </td>
                <td>
                    @{{ summary.forecast.sum | hideZero | number }}
                </td>
                <td>
                    @{{ summary.debt.sum | hideZero | number }}
                </td>
                @endif
                <td>@{{ summary.active_attachments.sum }}</td>
                <td>@{{ summary.new_clients.sum }}</td>
            </tr>
        </tbody>
    </table>
</div>
