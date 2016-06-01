@extends('app')
@section('controller', 'DebtMap')
@section('title', 'Дебет')

@section('scripts')
    <script src="//maps.google.ru/maps/api/js?libraries=places"></script>
    <script src="{{ asset('/js/maps.js', isProduction()) }}"></script>
    <script src="{{ asset('/js/markerclusterer.js', isProduction()) }}"></script>
@stop


@section('content')
    <div class="row mb">
        <div class="col-sm-12">
            <div class="options-list">
                <span ng-class="{'link-like': mode !== 'map'}" ng-click="mode = 'map'">карта</span>
                <span ng-class="{'link-like': mode !== 'list'}" ng-click="mode = 'list'">список</span>
            </div>
        </div>
    </div>
    <div class="row mb">
        <div class="col-sm-3">
            <div class="form-group">
                <div class="double-input">
                    <div class="input-group custom">
                      <span class="input-group-addon">дебет от </span>
                      <input type="text" class="form-control digits-only" ng-model="search.debt_from">
                    </div>
                    <div class="input-group custom">
                      <span class="input-group-addon">до </span>
                      <input type="text" class="form-control digits-only" ng-model="search.debt_to">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <div class="double-input">
                    <div class="input-group custom" style="width: 70%">
                      <span class="input-group-addon">расчетный дебет от </span>
                      <input type="text" class="form-control digits-only" ng-model="search.debt_calc_from">
                    </div>
                    <div class="input-group custom" style="width: 30%">
                      <span class="input-group-addon">до </span>
                      <input type="text" class="form-control digits-only" ng-model="search.debt_calc_to">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="double-input">
                    <div class="input-group custom" style="width: 60%">
                      <span class="input-group-addon">дата расчета от </span>
                      <input type="text" class="form-control bs-date-top" ng-model="search.account_date_from">
                    </div>
                    <div class="input-group custom" style="width: 40%">
                      <span class="input-group-addon">до </span>
                      <input type="text" class="form-control bs-date-top" ng-model="search.account_date_to">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                <button class="btn btn-primary full-width" ng-click='find()' ng-disabled='loading'>
                    @{{ loading ? 'поиск...' : 'найти' }}
                </button>
            </div>
        </div>
    </div>
    @include('debt.map._gmap')
    @include('debt.map._list')
@stop
