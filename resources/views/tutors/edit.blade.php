@extends('app')

@section('title')
    Редактирование преподавателя
    <a href='https://crm.a-perspektiva.ru/repetitors/edit/?id=@{{ tutor.id_a_pers }}' style='text-decoration: none' target="_blank">
        <span class='label label-white ng-hide' ng-show='tutor.id_a_pers'>старый номер в базе: <b>@{{ tutor.id_a_pers }}</b></span>
    </a>
@stop

@section('content')
@section('controller', 'TutorsForm')
@section('title-right')
    <span class="header-link" ng-click='mergeTutor()'>склеить</span>
    <a href="http://www.a-perspektiva.ru/tutors/?id=@{{ tutor.id_a_pers }}" target="_blank">анкета на a-perspektiva.ru</a>
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
