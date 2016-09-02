@extends('app')
@section('title', 'Договор')
@section('controller', 'ContractIndex')

@if (\App\Models\User::isDev() || \App\Models\User::isRoot())
    @section('title-right')
        {{ link_to('contract/edit', 'Изменить договор') }}
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
