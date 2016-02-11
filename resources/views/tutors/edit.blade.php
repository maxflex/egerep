@extends('app')
@section('title', 'Редактирование преподавателя new2')
@section('content')
@section('controller', 'TutorsForm')
@section('title-right')
    <a href="http://www.a-perspektiva.ru/tutors/?id={{ $id }}" target="_blank">анкета на a-perspektiva.ru</a>
    <a href="#">отчетность</a>
@endsection

<div class="row" ng-init="id = {{ $id }}">
    <div class="col-sm-12">

        @include('tutors._form')

        <div class="row">
            <div class="col-sm-12 center">
                <button class="btn btn-primary" ng-click="edit()" ng-disabled="saving">Сохранить</button>
            </div>
        </div>
    </div>
</div>

@stop
