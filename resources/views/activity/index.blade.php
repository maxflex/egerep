 @extends('app')
@section('title', 'Активность')
@section('controller', 'ActivityIndex')

@section('content')

<div class="row flex-list" style='width: 700px'>
    <div>
        <input readonly type="text" class="form-control bs-date-clear pointer" ng-model="search.date" placeholder="дата">
    </div>
    <div>
        @include('modules.user-select-light')
    </div>
    <div>
        <button type="button" class="btn btn-primary" ng-click='show()' ng-disabled="!search.user_id || !search.date">показать</button>
    </div>
</div>


<div class="row" style='margin-top: 15px'>
    <div class="col-sm-12">
        <div class="text-gray" ng-if="data && data == -1">
            нет данных
        </div>
        <table ng-if="data !== undefined && data != -1" class='activity-table'>
            <tr>
                <td class="padding">
                    <div style='display: inline-block; width: 320px'>
                        Первое зарегистрированное действие:
                    </div>
                </td>
                <td>
                    @{{ data.first_action_time }}
                </td>
            </tr>
            <tr>
                <td>
                    Наибольшие паузы:
                </td>
                <td class="padding">
                    <div ng-repeat="pause in data.pauses">
                        @{{ pause.start }} – @{{ pause.end }} (@{{ formatMinutes(pause.diff) }})
                    </div>
                </td>
            </tr>
            <tr>
                <td class="padding">
                    Последнее зарегистрированное действие:
                </td>
                <td>
                    @{{ data.last_action_time }}
                </td>
            </tr>
            <tr>
                <td>
                    Операций с базой данных:
                </td>
                <td>
                    @{{ data.database_operations }}
                </td>
            </tr>
            <tr>
                <td>
                    Просмотров URL:
                </td>
                <td>
                    @{{ data.url_views }}
                </td>
            </tr>
            <tr>
                <td>
                    Исходящих звонков успешных:
                </td>
                <td>
                    @{{ data.outgoing_calls_successful }}
                </td>
            </tr>
            <tr>
                <td>
                    Исходящих звонков неуспешных:
                </td>
                <td>
                    @{{ data.outgoing_calls_failed }}
                </td>
            </tr>
            <tr>
                <td>
                    Принятых звонков:
                </td>
                <td>
                    @{{ data.incoming_calls }}
                </td>
            </tr>
            <tr>
                <td>
                    Общее время разговоров:
                </td>
                <td>
                    @{{ formatMinutes(data.calls_duration) }}
                </td>
            </tr>
        </table>
    </div>
</div>

<style>
    .activity-table tr td {
        vertical-align: top;
    }
    td.padding {
        padding-bottom: 20px;
    }
</style>
@stop
