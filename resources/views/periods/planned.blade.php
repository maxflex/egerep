<div ng-if="type == 'planned'">
    <table class="table summary-table table-hover">
        <thead>
        <tr>
            <td>Преподаватель</td>
            <td>Дата расчета</td>
            <td>Тип расчета</td>
            <td>Пользователь</td>
        </tr>
        </thead>
        <tbody>
        <tr ng-repeat='period in periods'>
            <td><a href="tutors/@{{ period.tutor.id }}/edit">@{{ period.tutor.full_name || "имя не указано" }}</a></td>
            <td>@{{ shortenYear(period.date) }}</td>
            <td>@{{ LkPaymentTypes[period.payment_method] }}</td>
            <td>@{{ UserService.get(period.user_id).login }}</td>
        </tr>
        </tbody>
    </table>
</div>
