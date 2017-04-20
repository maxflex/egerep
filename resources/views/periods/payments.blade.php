<div ng-if="type == 'payments'">
    <div class="row mb">
        <div class="col-sm-12">
            <div class="options-list">
                <a class="link-like" href="{{ route('periods.index') }}">встречи</a>
                <span>платежи</span>
            </div>
        </div>
    </div>

    <div class="row flex-list" style='width: 60%'>
        <div>
            @include('modules.user-select-light')
        </div>
        <div>
            <select ng-model='search.method' class='selectpicker' ng-change='filter()'>
                <option value="">тип расчета</option>
                <option disabled>──────────────</option>
                <option ng-repeat='(id, name) in PaymentMethods' value="@{{id}}">@{{ name }}</option>
                <option value="-1">взаимозачёт</option>
            </select>
        </div>
        <div>
            <select ng-model='search.confirmed' class='selectpicker' ng-change='filter()'>
                <option value="">статус</option>
                <option disabled>──────────────</option>
                <option ng-repeat='(id, name) in Approved' value="@{{id}}">@{{ name }}</option>
            </select>
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
        <tr ng-repeat='payment in periods'>
            <td><a href="tutors/@{{ payment.tutor_id }}/accounts">
                <span ng-show='payment.tutor.first_name'>@{{ payment.tutor.last_name }} @{{ payment.tutor.first_name }} @{{ payment.tutor.middle_name }}</span>
                <span ng-show='!payment.tutor.first_name'>имя не указано</span>
            </a></td>
            <td><span ng-show='payment.sum'>@{{ payment.sum | number }}</span></td>
            <td>@{{ payment.id ? PaymentMethods[payment.method] : 'взаимозачёт' }}</td>
            <td>@{{ shortenYear(payment.date) }}</td>
            <td width='20%'>
                @{{ UserService.getLogin(payment.user_id) }} @{{ formatDateTime(payment.created_at) }}
            </td>
            <td width='100'>
                <span ng-if='payment.id' @if(allowed(\Shared\Rights::ER_EDIT_PAYMENTS))
                          class="link-like"
                          ng-click="toggleConfirmed(payment, AccountPayment)"
                      @endif
                      ng-class="{
                            'text-danger': !payment.confirmed,
                            'text-success': payment.confirmed
                          }">
                    @{{ Confirmed[payment.confirmed] }}
                </span>
                <span ng-if='!payment.id' ng-class="{
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
