@extends('app')
@section('title', 'Заявки')
@section('controller', 'RequestsIndex')

@section('title-right')
    {{ link_to_route('requests.create', 'добавить заявку') }}
@endsection

@section('content')
<div>
    <div class="row" ng-repeat="request in requests">
        <div class="col-sm-12">
            <a href="@{{ laroute.route('requests.edit', {requests: request.id}) }}">Заявка @{{ request.id }}</a>
            {{-- <span ng-bind-html="laroute.link_to_route('requests.edit', 'Заявка ', {teachers: teacher.id})"></span> --}}
        </div>
    </div>
</div>
@stop
