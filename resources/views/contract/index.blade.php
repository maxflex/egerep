@extends('app')
@section('title', 'Договор')
@section('controller', 'ContractIndex')

@if (allowed(\Shared\Rights::EDIT_CONTACT))
    @section('title-right')
        {{ link_to('contract/edit', 'изменить договор') }}
    @endsection
@endif

@section('content')
    <div class="row mb">
        <div class="col-sm-12">
            Договор от @{{ contract_date }}
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 contract-content" ng-bind-html="contract_html"></div>
    </div>
@stop
