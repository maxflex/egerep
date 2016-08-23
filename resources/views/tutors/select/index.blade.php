@extends('app')
@section('controller', 'TutorsSelect')
@section('title', 'Подбор преподавателя')

@section('title-right')
    {{-- <a href="requests/@{{ request_id }}/edit#@{{ list.id }}">вернуться к списку</a> --}}
@stop

@section('scripts')
    <script src="//maps.google.ru/maps/api/js?key=AIzaSyAXXZZwXMG5yNxFHN7yR4GYJgSe9cKKl7o&libraries=places"></script>
    <script src="{{ asset('/js/maps.js', isProduction()) }}"></script>
    <script src="{{ asset('/js/markerclusterer.js', isProduction()) }}"></script>
@stop


@section('content')
    <div class="row mb">
        <div class="col-sm-3">
            <div class="form-group">
                <div class="input-group custom">
                  <span class="input-group-addon">вывести анкету №</span>
                  <input type="text" class="form-control digits-only" ng-model="search.id">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group custom">
                  <span class="input-group-addon">фамилия –</span>
                  <input type="text" class="form-control" ng-model="search.last_name">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group custom">
                  <span class="input-group-addon">имя –</span>
                  <input type="text" class="form-control" ng-model="search.first_name">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group custom">
                  <span class="input-group-addon">отчество –</span>
                  <input type="text" class="form-control" ng-model="search.middle_name">
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <ng-multi object='Genders' model='search.gender' none-text='пол'></ng-multi>
            </div>
            <div class="form-group">
                <div class="double-input">
                    <div class="input-group custom">
                      <span class="input-group-addon">возраст от </span>
                      <input type="text" class="form-control digits-only" ng-model="search.age_from">
                    </div>
                    <div class="input-group custom">
                      <span class="input-group-addon">до </span>
                      <input type="text" class="form-control digits-only" ng-model="search.age_to">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <ng-multi none-text='классы' model='search.grades' object='Grades'></ng-multi>
            </div>
            <div class="form-group">
                <ng-multi none-text='предметы' model='search.subjects' object='Subjects.all'></ng-multi>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <div class="input-group custom">
                  <span class="input-group-addon">ТБ от</span>
                  <input type="text" class="form-control digits-only" ng-model="search.tb_from">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group custom">
                  <span class="input-group-addon">ЛК от</span>
                  <input type="text" class="form-control digits-only" ng-model="search.lk_from">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group custom">
                  <span class="input-group-addon">ЖС от</span>
                  <input type="text" class="form-control digits-only" ng-model="search.js_from">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group custom">
                  <span class="input-group-addon">стоимость занятия до</span>
                  <input type="text" class="form-control digits-only" ng-model="search.lesson_price_to">
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <ng-multi none-text='статус' model='search.state' object='TutorStates'></ng-multi>
            </div>
            <div class="form-group">
                <select ng-model='search.markers' class='selectpicker'>
                    <option value="">метки</option>
                    <option disabled>──────────────</option>
                    <option ng-repeat="name in ['метка есть', 'меток нет']"
                        value="@{{ $index + 1 }}">@{{ name }}</option>
                </select>
            </div>
            <div class="form-group">
                <select ng-model='search.ready' class='selectpicker'>
                    <option value="">заполненность поля «готовность»</option>
                    <option disabled>──────────────</option>
                    <option ng-repeat="(id, name) in YesNo"
                        value="@{{ (id*1) + 1 }}">@{{ name }}</option>
                </select>
            </div>
            <div class="form-group">
                <button class="btn btn-primary full-width" ng-click='find()' ng-disabled='loading'>
                    @{{ loading ? 'поиск...' : 'найти' }}
                </button>
            </div>
        </div>
    </div>
    <div class="row mb">
        <div class="col-sm-12">
            <div class="options-list">
                <span ng-class="{'link-like': mode !== 'map'}" ng-click="mode = 'map'">карта</span>
                <span ng-class="{'link-like': mode !== 'list'}" ng-click="mode = 'list'">список</span>
            </div>
        </div>
    </div>
    @include('tutors.select._gmap')
    @include('tutors.select._list')
@stop
