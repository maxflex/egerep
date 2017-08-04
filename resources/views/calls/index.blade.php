@extends('app')
@section('title', 'Звонки')
@section('controller', 'CallsIndex')

@section('content')
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
                </tr>
            </table>
        </div>
    </div>
    @include('modules.pagination')
@stop
