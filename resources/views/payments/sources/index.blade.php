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
        <tr ng-repeat="model in IndexService.page.data">
            <td>
                <a href='payments/sources/@{{ model.id }}/edit'>
                  @{{ model.name }}
                </a>
            </td>
            <td>
                  @{{ model.in_remainder | hideZero | number }}
            </td>
            <td>
                  @{{ model.loan_remainder | hideZero | number }}
            </td>
        </tr>
    </table>
    @include('modules.pagination-new')
@stop
