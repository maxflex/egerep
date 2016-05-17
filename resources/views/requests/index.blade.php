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
            <a href="requests/@{{ request.id }}/edit">Заявка @{{ request.id }}</a>
            {{-- <a href="@{{ laroute.route('requests.edit', {requests: request.id}) }}">Заявка @{{ request.id }}</a> --}}
            {{-- <span ng-bind-html="laroute.link_to_route('requests.edit', 'Заявка ', {teachers: teacher.id})"></span> --}}
        </div>
    </div>

    <pagination style="margin-top: 30px"
        ng-hide='data.last_page <= 1'
        ng-model="current_page"
        ng-change="pageChanged()"
        total-items="data.total"
        max-size="10"
        items-per-page="data.per_page"
        first-text="«"
        last-text="»"
        previous-text="«"
        next-text="»"
    >
    </pagination>
</div>
@stop
