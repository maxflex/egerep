@extends('app')

@section('title')
    Редактирование преподавателя
@stop

@section('content')
@section('controller', 'TutorsForm')
@section('title-right')
    <span class="header-link" ng-click='mergeTutor()'>склеить</span>
    <a target='_new' href="{{ $lk_link }}">режим просмотра</a>
    <a href="http://ege-repetitor.ru/tutors/person/@{{ tutor.id_a_pers }}" target="_blank">анкета на ege-repetitor.ru</a>
    <a href="tutors/{{ $id }}/accounts">отчетность</a>
    <span class="header-link" ng-click='deleteTutor()'>удалить</span>
@endsection

<div class="row" ng-init="id = {{ $id }}" id="tutorForm">
    <div class="col-sm-12">

        @include('tutors._form')

        <div class="row">
            <div class="col-sm-12 center">
                <button class="btn btn-primary" disabled ng-show="!form_changed">Сохранено</button>
                <button class="btn btn-primary" ng-show="form_changed" ng-click="edit()" ng-disabled="saving">Сохранить</button>
            </div>
        </div>
    </div>
</div>

@stop
