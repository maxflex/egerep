@extends('app')
@section('title', 'Стрим платежей')
@section('controller', 'PaymentsIndex')
@include('payments._top_right_section')
@include('payments._modals')

@section('content')
    <div class="row flex-list">
        <div>
            <label>источник</label>
            <select multiple title="не выбрано" ng-model="search.source_ids" class="selectpicker">
                <option ng-repeat="source in sources" value="@{{ source.id }}">@{{ source.name }}</option>
            </select>
        </div>
        <div>
            <label>адресат</label>
            <select multiple title="не выбрано" ng-model="search.addressee_ids" class="selectpicker">
                <option ng-repeat="source in sources" value="@{{ source.id }}">@{{ source.name }}</option>
            </select>
        </div>
        <div>
            <label>статья</label>
            <select multiple title="не выбрано" ng-model="search.expenditure_ids" class="selectpicker">
                <option ng-repeat="expenditure in expenditures" value="@{{ expenditure.id }}">@{{ expenditure.name }}</option>
            </select>
        </div>
        <div>
            <label>тип</label>
            <ng-select-new model='search.type' object="PaymentTypes" label="title" none-text='тип'></ng-select-new>
        </div>
    </div>

    <table class="table reverse-borders" style='font-size: 13px'>
        <tr ng-repeat="model in IndexService.page.data">
            <td>
                <a class="pointer" ng-click="editPayment(model)">@{{ findById(PaymentTypes, model.type).title }}</a>
            </td>
            <td>
                @{{ model.sum | number }}
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
            {{-- <td>
                @{{ UserService.getLogin(model.user_id) }}: @{{ formatDateTime(model.created_at) }}
            </td> --}}
            <td width="250">
                @{{ model.purpose | cut:false:20 }}
            </td>
        </tr>
    </table>
    @include('modules.pagination-new')
@stop

<style>
    label {
        margin: 0 0 2px 10px;
        color: #757575;
        font-size: 12px;
        font-weight: 500;
    }
</style>

{{-- drag & drop --}}
{{-- копировать платеж --}}
