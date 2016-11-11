<div ng-if="type == 'planned'">
    <table class="table summary-table table-hover">
        <thead>
        <tr>
            <td>ФИО</td>
            <td>дата расчета</td>
            <td>тип расчета</td>
            <td>пользователь</td>
        </tr>
        </thead>
        <tbody>
        <tr ng-repeat='period in periods'>
            <td><a href="tutors/@{{ period.tutor.id }}/edit">@{{ period.tutor.full_name || "имя не указано" }}</a></td>
            <td>@{{ period.date }}</td>
            <td>@{{ LkPaymentTypes[period.payment_method] }}</td>
            <td>@{{ UserService.get(period.user_id).login }}</td>
        </tr>
        </tbody>
    </table>
</div>
