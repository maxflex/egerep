@extends('app')
@section('title', 'Звонки')
@section('controller', 'CallsIndex')

@section('content')
    <div class="row flex-list">
        <div>
            @include('modules.user-select-light')
        </div>
        <div>
            <select ng-model='search.type' class='selectpicker' ng-change='filter()'>
                <option value="">тип звонка</option>
                <option disabled>──────────────</option>
                <option value="1">входящий</option>
                <option value="2">исходящий</option>
            </select>
        </div>
        <div>
            <select ng-model='search.line_number' class='selectpicker' ng-change='filter()'>
                <option value="">линия</option>
                <option disabled>──────────────</option>
                <option value="74956461080">74956461080</option>
                <option value="74956468592">74956468592</option>
            </select>
        </div>
        <div>
            <select ng-model='search.status_1' class='selectpicker' ng-change='filter()'>
                <option value="">@{{ CallStatuses[1] }}</option>
                <option disabled>──────────────</option>
                <option value="1">да</option>
                <option value="0">нет</option>
            </select>
        </div>
        <div>
            <select ng-model='search.status_2' class='selectpicker' ng-change='filter()'>
                <option value="">@{{ CallStatuses[2] }}</option>
                <option disabled>──────────────</option>
                <option value="1">да</option>
                <option value="0">нет</option>
            </select>
        </div>
        <div>
            <select ng-model='search.status_3' class='selectpicker' ng-change='filter()'>
                <option value="">@{{ CallStatuses[3] }}</option>
                <option disabled>──────────────</option>
                <option value="1">да</option>
                <option value="0">нет</option>
            </select>
        </div>
        <div>
            <select ng-model='search.status_4' class='selectpicker' ng-change='filter()'>
                <option value="">@{{ CallStatuses[4] }}</option>
                <option disabled>──────────────</option>
                <option value="1">да</option>
                <option value="0">нет</option>
            </select>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">
            <table class="table reverse-borders">
                <tr ng-repeat="call in calls">
                    <td>
                        @{{ formatTimestamp(call.start) }}
                    </td>
                    <td>
                        @{{ call.from_extension ? 'исходящий' : 'входящий' }}
                    </td>
                    <td>
                        @{{ UserService.getLogin(call.from_extension || call.to_extension) }}
                    </td>
                    <td>
                        @{{ call.from_extension ? call.to_number : call.from_number }}
                    </td>
                    <td>
                        @{{ callDuration(call) }}
                    </td>
                    <td>
                        <div ng-repeat="status in call.statuses">
                            @{{ CallStatuses[status + 1] }}<span ng-if="call.additional && status == 1"> (@{{ call.additional }})</span>
                        </div>
                    </td>
                    <td width='20'>
                        <span ng-show='call.recording_id' ng-click='play(call.recording_id)'
                            style='text-decoration: none' class="glyphicon no-margin-right link-like glyphicon-play" ng-class="{
                                'glyphicon-pause': isPlaying(call.recording_id) && is_playing_stage == 'play'
                            }"></span>
                    </td>
                    <td width='200'>
                        <div class="progress_bar" ng-show='isPlaying(call.recording_id)'>
                            <div class="wraperPGBR">
                                <div class="line" style="width: @{{prc}}%;"></div>
                            </div>
                            <div class="clicker" ng-click="setCurentTime($event)"></div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    @include('modules.pagination')
@stop
