{{-- FAKE DATES IF NO ACCOUNTS --}}
<div ng-if='tutor.last_accounts.length == 0'>
    <table class='accounts-table'>
        <thead>
            <tr>
                <td width='75'></td>
                <td ng-repeat='client in clients' width='50'>
                    клиент @{{ client.id }}
                </td>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat='date in getFakeDates()'>
                <td>@{{ formatDate(date) }}</td>
                <td ng-repeat='client in clients'>
                    <input type="text" class='account-column no-border-outline' ng-model='account.data[client.id][date]' disabled>
                </td>
            </tr>
        </tbody>
    </table>
</div>