@extends('app')
@section('title')
    Источники
    <a href="payments" class="title-link">назад в стрим</a>
@stop
@section('controller', 'PaymentSourceIndex')

@section('title-right')
    <a href="payments/sources/create">добавить</a>
@stop

@section('content')
    <table class="table reverse-borders">
        <thead>
            <tr>
                <td>

                </td>
                <td>
                    входящий остаток
                </td>
                <td>
                    заемный остаток
                </td>
            </tr>
        </thead>
        <tbody ui-sortable='sortableOptions' ng-model="IndexService.page.data">
            <tr ng-repeat="model in IndexService.page.data">
                <td width='400'>
                    <a href='payments/sources/@{{ model.id }}/edit'>
                      @{{ model.name }}
                    </a>
                </td>
                <td>
                      @{{ model.calc_remainder | hideZero | number }}
                </td>
                <td>
                      @{{ model.calc_loan_remainder | hideZero | number }}
                </td>
            </tr>
        </tbody>
    </table>
    @include('modules.pagination-new')
@stop
