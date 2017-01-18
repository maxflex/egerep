@extends('app')
@section('title', 'Стрим')
@section('controller', 'StreamIndex')

@section('content')

<div class="row flex-list">
    <div>
        <select ng-model='search.mobile' class='selectpicker' ng-change='filter()'>
            <option value="" data-subtext="@{{ counts.mobile[''] || '' }}">версия</option>
            <option disabled>──────────────</option>
            <option data-subtext="@{{ counts.mobile[0] || '' }}" value="0" ng-show="counts.mobile[0]">стационарная</option>
            <option data-subtext="@{{ counts.mobile[1] || '' }}" value="1" ng-show="counts.mobile[1]">мобильная</option>
        </select>
    </div>
    <div>
        <select ng-model='search.action' class='selectpicker' ng-change='filter()'>
            <option value="" data-subtext="@{{ counts.action[''] || '' }}">действие</option>
            <option disabled>──────────────</option>
            <option ng-repeat='action in actions' ng-show="counts.action[action]"
                data-subtext="@{{ counts.action[action] || '' }}"
                value="@{{action}}">@{{ action }}</option>
        </select>
    </div>
    <div>
        <select ng-model='search.type' class='selectpicker' ng-change='filter()'>
            <option value="" data-subtext="@{{ counts.type[''] || '' }}">тип действия</option>
            <option disabled>──────────────</option>
            <option ng-repeat='type in types' ng-show="counts.type[type]"
                data-subtext="@{{ counts.type[type] || '' }}"
                value="@{{type}}">@{{ type }}</option>
        </select>
    </div>
</div>

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
                    <td class="mobile-cell" width="5">
                        <i ng-show="s.mobile" class="fa fa-mobile" aria-hidden="true" style='font-size: 14px'></i>
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
                        <a ng-show="s.tutor_id" ng-href="tutors/@{{ s.tutor_id }}/edit">@{{ s.tutor_id  }}</a>
                    </td>
                    <td width="3%">
                        @{{ s.position }}
                    </td>
                    <td width="3%">
                        <span ng-show='s.search'>@{{ s.search }}</span>
                    </td>
                    <td width="2%">
                        @{{ s.page }}
                    </td>
                    <td width="30%">
                        <div ng-show="s.action == 'filter'">
                            <div>
                                @{{ s.place ? findById(places, s.place).title : 'неважно где' }}
                            </div>
                            <div ng-show='s.sort'>
                                по @{{ findById(sort, s.sort).title }}
                            </div>
                            <div ng-show="s.subjects.length">
                                <span ng-repeat='subject_id in s.subjects'>
                                    @{{ Subjects.all[subject_id] }}@{{ $last ? '' : ', '}}
                                </span>
                            </div>
                            <div ng-show="s.station_id">
                                @{{ findById(stations, s.station_id).title }}
                            </div>
                        </div>
                    </td>
                    <td width="10%">
                        @{{ formatDateTime(s.created_at) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@include('modules.pagination')
@stop
