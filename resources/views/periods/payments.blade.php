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
        <tr ng-repeat='payment in payments'>
            <td><a href="tutors/@{{ payment.tutor_id }}/accounts">
                <span ng-show='payment.tutor.last_name'>@{{ payment.tutor.last_name }} @{{ payment.tutor.first_name }} @{{ payment.tutor.middle_name }}</span>
                <span ng-show='!payment.tutor.last_name'>имя не указано</span>
            </a></td>
            <td>@{{ payment.sum | number }}</td>
            <td>@{{ PaymentMethods[payment.method] }}</td>
            <td>@{{ shortenYear(payment.date) }}</td>
            <td width='20%'>
                @{{ UserService.getLogin(payment.user_id) }} @{{ formatDateTime(payment.created_at) }}
            </td>
            <td width='100'>
                <span @if(allowed(\Shared\Rights::EDIT_PAYMENTS))
                          class="link-like"
                          ng-click="toggleConfirmed(payment, AccountPayment)"
                      @endif
                      ng-class="{
                            'text-danger': !payment.confirmed,
                            'text-success': payment.confirmed
                          }">
                    @{{ Confirmed[payment.confirmed] }}
                </span>
            </td>
        </tr>
        </tbody>
    </table>
</div>
