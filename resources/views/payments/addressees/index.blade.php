@extends('app')
@section('title')
    Адресаты
    <a href="payments" class="title-link">назад в стрим</a>
@stop
@section('controller', 'PaymentAddresseeIndex')

@section('title-right')
    <a href="payments/addressees/create">добавить</a>
@stop

@section('content')
    <table class="table reverse-borders">
        <tr ng-repeat="model in IndexService.page.data">
            <td>
                <a href='payments/addressees/@{{ model.id }}/edit'>
                  @{{ model.name }}
                </a>
            </td>
        </tr>
    </table>
    @include('modules.pagination-new')
@stop
