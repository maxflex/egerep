@extends('app')
@section('title', 'Стрим')
@section('controller', 'StreamIndex')

@section('content')

<div class="row">
    <div class="col-sm-12">
        <table class="table" style="font-size: 0.8em;">
            <thead>
                <td></td>
                <td>клиент</td>
                <td>источник</td>
                <td>предметы</td>
                <td>где</td>
                <td>сортировка</td>
                <td>метро</td>
                <td>позиция</td>
                <td>поиск</td>
                <td>время</td>
            </thead>
            <tbody>
                <tr ng-repeat='s in stream'>
                    <td>
                        @{{ s.id }}
                    </td>
                    <td>
                        @{{ s.client_id }}</a>
                    </td>
                    <td>
                        @{{ s.source }}
                    </td>
                    <td>
                        <span ng-repeat='subject_id in s.subjects'>
                            @{{ Subjects.all[subject_id] }}@{{ $last ? '' : ', '}}
                        </span>
                    </td>
                    <td>
                        <span ng-show='s.sort'>
                            @{{ s.place ? findById(places, s.place) : 'неважно где' }}
                        </span>
                    </td>
                    <td>
                        <span ng-show='s.sort'>по @{{ findById(sort, s.sort).title }}</span>
                    </td>
                    <td>
                        <span ng-show="s.station_id">@{{ findById(stations, s.station_id).title }}</span>
                    </td>
                    <td>
                        @{{ s.position }}
                    </td>
                    <td>
                        @{{ s.search }}
                    </td>
                    <td>
                        @{{ formatDateTime(s.created_at) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@include('modules.pagination')
@stop
