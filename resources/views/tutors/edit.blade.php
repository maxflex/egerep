@extends('app')

@section('title')
    Редактирование преподавателя
@stop

@section('content')
@section('controller', 'TutorsForm')
@section('title-right')
    <span style="position: absolute; left: 45%;" class="link-like link-reverse link-white" ng-show='!form_changed'>cохранено</span>
    <span style="position: absolute; left: 45%;" class="link-like link-reverse link-white" ng-show="form_changed" ng-click="edit()" ng-disabled="saving">cохранить</span>
    @if($user->allowed(\Shared\Rights::ER_MERGE_TUTOR))
    <span class="header-link" ng-click='mergeTutor()'>склеить</span>
    @endif
    <a href="http://test.ege-repetitor.ru/@{{ tutor.id }}" target="_blank">анкета на ege-repetitor.ru</a>
    <a href="tutors/{{ $id }}/accounts">отчетность</a>
    @if($user->allowed(\Shared\Rights::ER_DELETE_TUTOR))
    <span class="header-link" ng-click='deleteTutor()'>удалить</span>
    @endif
@endsection

<div class="row" ng-init="id = {{ $id }}" id="tutorForm">
    <div class="col-sm-12">

        @include('tutors._form')

    </div>
</div>

@stop
