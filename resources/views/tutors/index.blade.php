@extends('app')
@section('title', 'Преподаватели')
@section('controller', 'TutorsIndex')

@section('title-right')
    {{ link_to_route('tutors.create', 'добавить преподавателя') }}
@endsection

@section('content')
<div>
    <div class="row" ng-repeat="tutor in tutors">
        <div class="col-sm-12">
            <span ng-bind-html="laroute.link_to_route('tutors.edit', tutor.full_name, {tutors: tutor.id})"></span>
        </div>
    </div>
</div>
@stop
