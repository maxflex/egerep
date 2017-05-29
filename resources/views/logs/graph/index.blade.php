@extends('app')
@section('title', 'График активности')
@section('controller', 'LogsGraph')

@section('title-right')
    {{-- нужно поправить функция link_to_route, чтобы она работала с https --}}
    {{-- {{ link_to_route('tutors.create', 'добавить преподавателя') }} --}}
@endsection

@section('content')
    <div class="row flex-list" style='width: 50%'>
        <div>
            <select class="form-control selectpicker" multiple ng-change='filter()'
                ng-model='search.user_ids' data-none-selected-text="пользователь">
                <option
                    ng-repeat="user in UserService.getAll()"
                    value="@{{ user.id }}"
                    data-content="<span style='color: @{{ user.color || 'black' }}'>@{{ user.login }}</span>"
                ></option>
                <option
                        ng-repeat="user in UserService.getBannedUsers()"
                        value="@{{ user.id }}"
                        data-content="<span style='color: @{{ user.color || 'black' }}'>@{{ user.login }}</span>"
                ></option>
            </select>
        </div>
        <div>
            <select ng-model='search.period' class='selectpicker' ng-change='filter()'>
                <option ng-repeat='(id, name) in LogPeriods'
                    data-subtext="@{{ counts.type[id] || '' }}"
                    value="@{{id}}">@{{ name }}</option>
            </select>
        </div>
    </div>

    <div style='position: relative' ng-class="{'zero-opacity': !chart.data.datasets.length}">
        <div class="frontend-loading animate-fadeIn" style='height: 101%; width: 101%' ng-show='loading'>
            <span>загрузка...</span>
        </div>
        <canvas id="myChart" height='100'></canvas>
    </div>
    <div ng-show='!chart.data.datasets.length' class='no-graph-data'>
        нет данных
    </div>
@stop
