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
        <tbody ui-sortable='sortableOptions' ng-model="IndexService.page.data">
            <tr ng-repeat="model in IndexService.page.data">
                <td>
                    <a href='payments/sources/@{{ model.id }}/edit'>
                      @{{ model.name }}
                    </a>
                </td>
                <td>
                    @{{ model.remainder | number }}
                </td>
                <td>
                    @{{ model.remainder_date }}
                </td>
            </tr>
        </tbody>
    </table>
    @include('modules.pagination-new')
@stop
