@extends('app')
@section('title')
    Статьи
    <a href="payments" class="title-link">назад в стрим</a>
@stop
@section('controller', 'PaymentExpenditureIndex')

@section('title-right')
    <a href="payments/expenditures/create">добавить</a>
@stop

@section('content')
    <table class="table reverse-borders">
        <tr ng-repeat="model in IndexService.page.data">
            <td>
                <a href='payments/expenditures/@{{ model.id }}/edit'>
                  @{{ model.name }}
                </a>
            </td>
        </tr>
    </table>
    @include('modules.pagination-new')
@stop