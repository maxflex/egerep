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
            <a href='tutors/@{{ tutor.id }}/edit'>@{{ tutor.full_name }}</a>
        </div>
    </div>
</div>

<pagination style="margin-top: 30px"
  ng-model="current_page"
  total-items="data.total"
  max-size="10"
  items-per-page="data.per_page"
  first-text="«"
  last-text="»"
  previous-text="«"
  next-text="»"
>
</pagination>
@stop
