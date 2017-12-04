@extends('app')
@section('title')
    Остатки
    <a href="payments" class="title-link">назад в стрим</a>
@stop
@section('controller', 'PaymentRemainders')

@section('content')
    <div class="row flex-list" style="width: 300px">
        <div>
            <label>счёт</label>
            <select title="не выбрано" ng-model="source_id" class="selectpicker" ng-change="filterChanged()">
                <option value="">не выбрано</option>
                <option disabled>──────────────</option>
                <option ng-repeat="source in sources" value="@{{ source.id }}">@{{ source.name }}</option>
            </select>
        </div>
    </div>
    <table class="table table-hover reverse-borders">
        <tbody ng-repeat="(date, items) in data.items">
            <tr ng-if="data.remainders[date]">
                <td colspan="4"></td>
                <td>@{{ data.remainders[date].sum | number }}</td>
                <td colspan="2" class="text-gray">
                    @{{ data.totals[date].comment }}
                </td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td colspan="2">@{{ data.totals[date].sum | number }}</td>
                <td colspan="2" class="text-gray">
                    @{{ data.totals[date].comment }}
                </td>
            </tr>
            <tr ng-repeat="item in items" ng-class="{'last-date': $last}">
                <td width='120'>
                    <span ng-show="$last">@{{ date | date:'dd.MM.yyyy' }}</span>
                </td>
                <td width='120'>
                    <span ng-show="item.addressee_id == source_id" class="text-success">+@{{ item.sum | number }}</span>
                </td>
                <td width='120'>
                    <span ng-show="item.source_id == source_id" class="text-danger">-@{{ item.sum | number }}</span>
                </td>
                <td width='120'>
                </td>
                <td width='120'>
                </td>
                <td width='300'>
                    <span ng-if="item.expenditure_id">@{{ expenditures[item.expenditure_id].name }}</span>
                    {{-- <span ng-if="item.expenditure_id == -1">входящий остаток</span> --}}
                </td>
                <td>
                    @{{ item.purpose }}
                </td>
            </tr>
        </tbody>
    </table>

    <pagination
          ng-model="current_page"
          ng-change="pageChanged()"
          ng-hide="!data || data.item_cnt < {{ \App\Models\Payment\Source::PER_PAGE_REMAINDERS }}"
          total-items="data.item_cnt"
          max-size="10"
          items-per-page="{{ \App\Models\Payment\Source::PER_PAGE_REMAINDERS }}"
          first-text="«"
          last-text="»"
          previous-text="«"
          next-text="»"
    >
    </pagination>
@stop

<style>
    .table > tbody + tbody {
        border-top: none !important;
    }
    .table tr td {
        font-size: 12px;
    }
    .table tr td {
        border-bottom: none !important;
    }
    .table tr.last-date td {
        border-bottom: 1px solid #ddd !important;
    }
</style>