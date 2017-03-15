@extends('app')
@section('title', 'Проверка работоспособности конфигураций')
@section('controller', 'StreamConfigurations')

@section('title-right')
    <a href='stream'>назад</a>
@stop

@section('content')

<table class="table">
    <thead>
        <tr>
            <td style='width: 5px'>
            </td>
            <td>
                действие
            </td>
            <td>
                тип действия
            </td>
            <td>
                дата последней регистрации
            </td>
        </tr>
    </thead>
    <tbody>
        <tr ng-repeat="d in data | orderBy:'created_at':true">
            <td>
                <i ng-show="d.mobile" class="fa fa-mobile" aria-hidden="true" style='font-size: 14px'></i>
            </td>
            <td>
                @{{ d.action }}
            </td>
            <td>
                @{{ d.type }}
            </td>
            <td>
                @{{ formatDateTime(d.created_at) }}
            </td>
        </tr>
    </tbody>
</table>
@stop
