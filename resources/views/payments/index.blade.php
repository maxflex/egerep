@extends('app')
@section('title', 'Стрим платежей')
@section('controller', 'PaymentsIndex')
@include('payments._top_right_section')

@section('content')
    <div class="row flex-list">
        <div>
            <select ng-highlight class="form-control selectpicker" ng-model='search.user_id' id='change-user'>
                <option value=''>пользователь</option>
            	<option disabled>──────────────</option>
            	<option
            		ng-repeat="user in UserService.getActiveInAnySystem()"
            		value="@{{ user.id }}"
            		data-content="<span style='color: @{{ user.color || 'black' }}'>@{{ user.login }}</span>"
            	></option>
            	<option disabled>──────────────</option>
            	<option
                    ng-repeat="user in UserService.getBannedInBothSystems()"
            		value="@{{ user.id }}"
            		data-content="<span style='color: black'>@{{ user.login }}</span>"
            	></option>
            </select>
        </div>
        <div>
            <ng-select-new model='search.addressee_id' object="sources" label="name" none-text='адресат'></ng-select-new>
        </div>
        <div>
            <ng-select-new model='search.source_id' object="sources" label="name" none-text='источник'></ng-select-new>
        </div>
        <div>
            <ng-select-new model='search.expenditure_id' object="expenditures" label="name" none-text='статья'></ng-select-new>
        </div>
        <div>
            <ng-select-new model='search.loan' object="PaymentTypes" label="title" none-text='тип'></ng-select-new>
        </div>
    </div>

    <table class="table reverse-borders">
        <tr ng-repeat="model in IndexService.page.data">
            <td>
                @{{ findById(PaymentTypes, model.loan).title }}
            </td>
            <td>
                @{{ model.sum | number }} руб.
            </td>
            <td>
                @{{ model.date }}
            </td>
            <td>
                @{{ findById(sources, model.source_id).name }}
            </td>
            <td>
                @{{ findById(sources, model.addressee_id).name }}
            </td>
            <td>
                @{{ findById(expenditures, model.expenditure_id).name }}
            </td>
            <td>
                @{{ UserService.getLogin(model.user_id) }}: @{{ formatDateTime(model.created_at) }}
            </td>
            <td style='text-align: right'>
                <a href="payments/@{{ model.id }}/edit">редактировать</a>
            </td>
        </tr>
    </table>
    @include('modules.pagination-new')
@stop
