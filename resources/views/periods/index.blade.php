@extends('app')
@section('title', 'Создание периодов')
@section('controller', 'PeriodsIndex')

@section('content')
<div>
    <table class="table">
        <thead>
            <tr>
                <td>Преподаватель</td>
                <td>Время проводки</td>
                <td>Дата расчета</td>
                <td>Пользователь</td>
                <td>Было в дебете</td>
                <td>Переданная сумма</td>
                <td>Доход</td>
                <td>Задолженность</td>
                <td>Метод расчета</td>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat='period in periods'>
                <td>
                    <a href="tutors/@{{ period.tutor.id }}/edit">@{{ period.tutor.full_name }}</a>
                </td>
                <td>@{{ formatDateTime(period.created_at) }}</td>
                <td>
                    @{{ formatDate(period.date_end) }}
                </td>
                <td>@{{ period.user_login }}</td>
                <td>@{{ period.debt_before | number }}</td>
                <td>@{{ period.received | number }}</td>
                <td>
                    @{{ totalCommission(period) }}
                </td>
                <td>

                    <span ng-class="{
                        'text-danger': period.debt_type == 0,
                        'text-success': period.debt_type == 1,
                    }">@{{ period.debt }}</span>
                </td>
                <td>
                    @{{ PaymentMethods[period.payment_method] }}
                </td>
            </tr>
        </tbody>
    </table>
</div>
<pagination style="margin-top: 30px"
        ng-hide='data.last_page <= 1'
        ng-model="current_page"
        ng-change="pageChanged()"
        total-items="data.total"
        max-size="10"
        items-per-page="data.per_page"
        first-text="«"
        last-text="»"
        previous-text="«"
        next-text="»"
    >
</pagination>
@stop
