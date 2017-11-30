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
        @if (allowed(9998))
        <tbody ui-sortable='sortableOptions' ng-model="IndexService.page.data">
            <tr ng-repeat="model in IndexService.page.data">
                <td>
                    <a href='payments/sources/@{{ model.id }}/edit'>@{{ model.name }}</a>
                </td>
            </tr>
        </tbody>
        @else
            <tr ng-repeat="model in IndexService.page.data">
                <td>
                    @{{ model.name }}
                </td>
            </tr>
        @endif
    </table>
    @include('modules.pagination-new')
@stop
