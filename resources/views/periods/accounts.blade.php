<div ng-if="type == 'accounts'">
    <div class="row mb">
        <div class="col-sm-12">
            <div class="options-list">
                <a class="link-like" href="{{ route('periods.index') }}">встречи</a>
                <span>платежи</span>
            </div>
        </div>
    </div>

    <table class="table summary-table table-hover">
        <thead>
        <tr>
            <td>Преподаватель</td>
            <td>Сумма</td>
            <td>Тип расчета</td>
            <td>Дата расчета</td>
            <td>Реквизиты</td>
        </tr>
        </thead>
        <tbody>
        <tr ng-repeat='period in periods'>
            <td><a href="tutors/@{{ period.account.tutor.id }}/accounts">@{{ period.account.tutor.full_name || "имя не указано" }}</a></td>
            <td>@{{ period.sum | number }}</td>
            <td>@{{ PaymentMethods[period.method] }}</td>
            <td>@{{ shortenYear(period.date) }}</td>
            <td width='20%'>
                @{{ UserService.getLogin(period.user_id) }}: @{{ formatDateTime(period.created_at) }}
            </td>
        </tr>
        </tbody>
    </table>
</div>
