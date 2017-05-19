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
            @include('modules.user-select-light')
        </div>
        <div>
            <select ng-model='search.period' class='selectpicker' ng-change='filter()'>
                <option value="" data-subtext="@{{ counts.type[''] || '' }}">период</option>
                <option disabled>──────────────</option>
                <option ng-repeat='(id, name) in LogPeriods'
                    data-subtext="@{{ counts.type[id] || '' }}"
                    value="@{{id}}">@{{ name }}</option>
            </select>
        </div>
    </div>
    
    <canvas class="chart chart-line" chart-data="data.data" chart-labels="data.labels" chart-series="data.series"></canvas>
@stop
