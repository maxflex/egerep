<div ng-if="type == 'debtors' && user.show_summary">
    <table class="table table-hover">
        <thead class="text-center">
            <tr>
                <td width="150">
                </td>
                <td>
                    всего вечных должников
                </td>
                <td>
                    сумма долга
                </td>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="(date, summary) in summaries">
                <td>
                    @{{ date | date:'dd MMMM yyyy' }}
                </td>
                <td class="text-center">
                    @{{ summary.cnt | hideZero }}
                </td>
                <td class="text-center">
                    @{{ getSummarySum(summary) | hideZero | number }}
                </td>
            </tr>
        </tbody>
    </table>
</div>
