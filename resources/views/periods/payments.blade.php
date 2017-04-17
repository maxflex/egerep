<div ng-if="type == 'payments'">
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
            <td>Статус</td>
        </tr>
        </thead>
        <tbody>
        <tr ng-repeat='period in periods'>
            <td><a href="tutors/@{{ period.account.tutor.id }}/accounts">@{{ period.account.tutor.full_name || "имя не указано" }}</a></td>
            <td>@{{ period.sum | number }}</td>
            <td>@{{ PaymentMethods[period.method] }}</td>
            <td>@{{ shortenYear(period.date) }}</td>
            <td width='20%'>
                @{{ UserService.getLogin(period.user_id) }} @{{ formatDateTime(period.created_at) }}
            </td>
            <td>
                <span @if(allowed(\Shared\Rights::EDIT_PAYMENTS))
                          class="link-like"
                          ng-click="toggleConfirmed(period, AccountPayment)"
                      @endif
                      ng-class="{
                            'text-danger': !period.confirmed,
                            'text-success': period.confirmed
                          }">
                    @{{ Confirmed[period.confirmed] }}
                </span>
            </td>
        </tr>
        </tbody>
    </table>
</div>
