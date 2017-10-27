@extends('app')
@section('title')
    Остатки
    <a href="payments" class="title-link">назад в стрим</a>
@stop
@section('controller', 'PaymentRemainders')

@section('content')
    <table class="table table-hover summary-table">
        <thead>
            <tr>
                <td width="150">
                </td>
                <td ng-repeat="source in sources">
                    @{{ source.name }}
                </td>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="(date, srs) in data">
                <td>
                    @{{ date | date:'dd MMMM yyyy' }}
                </td>
                <td ng-repeat="source in srs">
                    @{{ source.remainder }}
                    {{-- /@{{ source.loan_remainder }} --}}
                </td>
            </tr>
        </tbody>
    </table>

    <pagination
          ng-model="current_page"
          ng-change="pageChanged()"
          ng-hide="false"
          total-items="item_cnt"
          max-size="10"
          items-per-page="{{ \App\Models\Payment\Source::PER_PAGE_REMAINDERS }}"
          first-text="«"
          last-text="»"
          previous-text="«"
          next-text="»"
    >
    </pagination>
@stop
