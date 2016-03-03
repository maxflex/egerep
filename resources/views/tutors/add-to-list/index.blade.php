@extends('app')
@section('controller', 'AddToList')
@section('title', 'Добавление преподавателя')

@section('scripts')
    <script src="//maps.google.ru/maps/api/js?libraries=places"></script>
    <script src="{{ asset('/js/maps.js') }}"></script>
@endsection

@section('content')
<div class="row">
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
            {{-- <select class="form-control" id='directive-select' multiple
                ng-model="search.gender"
                ng-options="object_id as label for (object_id, label) in Genders">
            </select> --}}
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
            <ng-select model='search.destination' object='Destinations'></ng-select>
            {{-- <ng-multi model='search.destination' object='Destinations' none-text='фояр лапута'></ng-multi> --}}
        </div>
        {{-- <div class="form-group" style="visibility: hidden">
            <input type="text" class="form-control digits-only">
        </div> --}}
        <div class="form-group">
            <button class="btn btn-primary full-width">найти</button>
        </div>
    </div>
</div>
@stop
