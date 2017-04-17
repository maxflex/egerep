<div ng-if="type == 'total'">
    <div class="row mb">
        <div class="col-sm-12">
            <div class="options-list">
                <span>встречи</span>
                <a class="link-like" href="{{ route('periods.payments') }}">платежи</a>
            </div>
        </div>
    </div>

    <table class="table summary-table table-hover">
        <thead>
        <tr>
            <td>Преподаватель</td>
            <td>Время проводки</td>
            <td>Дата расчета</td>
            <td>Пользователь</td>
            <td>Дебет</td>
            <td>Передано</td>
            <td>Доход</td>
            <td>Долг</td>
            <td>Статус</td>
        </tr>
        </thead>
        <tbody>
        <tr ng-repeat='period in periods'>
            <td>
                <a href="tutors/@{{ period.tutor.id }}/edit">@{{ period.tutor.full_name || "имя не указано" }}</a>
            </td>
            <td>@{{ formatDateTime(period.created_at) }}</td>
            <td>
                @{{ formatDate(period.date_end) }}
            </td>
            <td>@{{ period.user_login }}</td>
            <td>@{{ period.debt_calc | hideZero | number}}</td>
            <td ng-init="_sum = getSum(period.all_payments)">
                <span ng-show='_sum[0]'>@{{ _sum[0] | number }}</span>
                <span class='mutual-debt' ng-if="_sum[1]"><span ng-show='_sum[0]'>+</span>@{{ _sum[1] }}</span>
            </td>
            <td>
                @{{ totalCommission(period) | number }}
            </td>
            <td>
                    <span ng-class="{
                        'text-danger': period.debt_type == 0,
                        'text-success': period.debt_type == 1,
                    }">@{{ period.debt }}</span>
            </td>
            <td>
                <span @if(allowed(\Shared\Rights::ER_EDIT_ACCOUNTS))
                      class="link-like"
                      ng-click="toggleConfirmed(period, Account)"
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
