@extends('app')
@section('title', 'Стрим')
@section('controller', 'StreamIndex')

@section('content')

<div class="row">
    <div class="col-sm-12">
        <table id="stream-data" class="table" style="font-size: 0.8em;">
            <thead>
                <td></td>
                <td>клиент</td>
                <td>действие</td>
                <td>тип</td>
                <td>преподаватель</td>
                <td>позиция</td>
                <td>поиск</td>
                <td>страница</td>
                <td>серп</td>
                <td>время</td>
            </thead>
            <tbody>
                <tr ng-repeat="s in stream">
                    <td class="mobile-cell" width="1%">
                        <i ng-show="s.mobile" class="fa fa-mobile" aria-hidden="true"></i>
                    </td>
                    <td width="10%">
                        @{{ s.google_id }}
                    </td>
                    <td width="8%">
                        @{{ s.action }}
                    </td>
                    <td width="8%">
                        @{{ s.type  }}
                    </td>
                    <td width="20%">
                        <a ng-show="s.tutor_id" ng-href="tutors/@{{ s.tutor_id }}/edit">@{{ s.tutor.full_name  }}</a>
                    </td>
                    <td width="3%">
                        @{{ s.position }}
                    </td>
                    <td width="3%">
                        @{{ s.search }}
                    </td>
                    <td width="2%">
                        @{{ s.page }}
                    </td>
                    <td width="30%">
                        <table>
                            <tr>
                                <td>место:</td>
                                <td>@{{ s.place ? findById(Places, s.place).title : 'неважно где' }}</td>
                            </tr>
                            <tr ng-show='s.sort'>
                                <td>сортировка:</td>
                                <td>по @{{ findById(Sort, s.sort).title }}</td>
                            </tr>
                            <tr ng-show="s.subjects.length">
                                <td>предметы:</td>
                                <td>
                                    <span ng-repeat='subject_id in s.subjects'>
                                        @{{ Subjects.all[subject_id] }}@{{ $last ? '' : ', '}}
                                    </span>
                                </td>
                            </tr>
                            <tr ng-show="s.station_id">
                                <td>метро:</td>
                                <td>
                                    @{{ findById(stations, s.station_id).title }}
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td swidth="10%">
                        @{{ formatDateTime(s.created_at) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@include('modules.pagination')
@stop
