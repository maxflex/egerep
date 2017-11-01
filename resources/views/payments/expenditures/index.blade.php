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
    <div ui-sortable='sortableGroupOptions' ng-model="groups">
        <div ng-repeat="group in groups" ng-class="{'item-draggable': group.id}" ng-hide="!group.id && !group.data.length">
            <h4 editable='@{{ group.id }}' class="inline-block" ng-class="{'disable-events': !group.id}">@{{ group.name }}</h4>
            <a ng-if='group.id' style='margin-left: 5px' class='link-like text-danger show-on-hover' ng-click='removeGroup(group)'>удалить</a>
            <table class="table reverse-borders">
                <tbody ui-sortable='sortableOptions' ng-model="group.data">
                    <tr ng-repeat="model in group.data">
                        <td>
                            <a href='payments/expenditures/@{{ model.id }}/edit'>
                              @{{ model.name }}
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@stop
