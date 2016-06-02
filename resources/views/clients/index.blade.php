@extends('app')
@section('title', 'Клиенты')
@section('controller', 'ClientsIndex')

@section('content')
<div>
    <div class="row" ng-repeat="client in clients">
        <div class="col-sm-12">
            <a href="client/@{{ client.id }}">@{{ client.name || 'имя не указано' }}</a>
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
