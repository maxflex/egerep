<div ng-if="type == 'payments' && user.show_summary">
    <table class="table table-hover">
        <thead class="text-center">
            <tr>
                <td width="150">
                </td>
                <td ng-repeat="(method, label) in PaymentMethods">
                    @{{ label }}
                </td>
                <td>
                    взаимозачет
                </td>
                <td class="left-border">все платежи</td>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="(date, summary) in summaries">
                <td>
                    @{{ date | date:'dd MMMM yyyy' }}
                </td>
                <td ng-repeat="(method, label) in PaymentMethods" class="text-center">
                    @{{ summary.received[method].sum | hideZero | number }}
                </td>
                <td class="text-center">
                    @{{ summary.mutual_debts.sum | hideZero | number }}
                </td>
                <td class="left-border text-center">
                    @{{ summary.total | hideZero | number }}
                </td>
            </tr>
        </tbody>
    </table>
</div>
