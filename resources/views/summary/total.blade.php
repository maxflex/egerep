<div ng-if="type == 'total'">
    <table class="table table-hover">
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
                <td ng-show="user.show_summary">
                    получено
                </td>
                <td ng-show="user.show_summary">
                    комиссия
                </td>
                <td ng-show="user.show_summary">
                    прогноз
                </td>
                <td ng-show="user.show_summary">
                    дебет
                </td>
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
                <td ng-show="user.show_summary">
                    <span title="@{{ summary.received.sum | number }} + @{{ (summary.mutual_debts.sum ? summary.mutual_debts.sum : 0) | number }}">
                        @{{ +(summary.received.sum) + +(summary.mutual_debts.sum) | hideZero | number }}
                    </span>
                </td>
                <td ng-show="user.show_summary">
                    @{{ summary.commission.sum | hideZero | number }}
                </td>
                <td ng-show="user.show_summary">
                    @{{ summary.forecast.sum | hideZero | number }}
                </td>
                <td ng-show="user.show_summary">
                    @{{ summary.debt.sum | hideZero | number }}
                </td>
                <td>@{{ summary.active_attachments.sum }}</td>
                <td>@{{ summary.new_clients.sum }}</td>
            </tr>
        </tbody>
    </table>
</div>
