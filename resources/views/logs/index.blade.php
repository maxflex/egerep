@extends('app')
@section('title', 'Логи')
@section('controller', 'LogsIndex')

@section('content')

<div class="row flex-list">
    <div>
        <select class="form-control selectpicker" ng-model='search.user_id' ng-change="filter()" id='change-user'>
            <option value="">пользователь</option>
            <option disabled>──────────────</option>
            <option
                ng-repeat="user in UserService.getWithSystem()"
                value="@{{ user.id }}"
                data-content="<span style='color: @{{ user.color || 'black' }}'>@{{ user.login }} @{{ $var }}</span><small class='text-muted'>@{{ counts.user[user.id] || '' }}</small>"
            ></option>
        </select>
    </div>
    <div>
        <div class="form-group">
            <div class="input-group custom">
              <span class="input-group-addon">дата начала –</span>
              <input type="text"
                  class="form-control bs-date-top" ng-model="search.date_start">
            </div>
        </div>
    </div>
    <div>
        <div class="form-group">
            <div class="input-group custom">
              <span class="input-group-addon">дата конца –</span>
              <input type="text"
                  class="form-control bs-date-top" ng-model="search.date_end">
            </div>
        </div>
    </div>
    <div style="margin-right: 0">
        <button class="btn btn-primary full-width" ng-click='filter()'>поиск</button>
    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <table class="table reverse-borders" style="font-size: 12px">
            <thead>
                <td></td>
                <td></td>
                <td></td>
            </thead>
            <tbody>
                <tr ng-repeat='log in logs'>
                    <td>
                        @{{ log.table }}
                    </td>
                    <td>
                        @{{ log.type }}
                    </td>
                    <td>
                        @{{ log.row_id }}
                    </td>
                    <td width="100">
                        <user model='log.user'></user>
                    </td>
                    <td ng-init='d = toJson(log.data)'>
                        <table style="font-size: 12px">
                            <tr ng-repeat="(key, data) in d track by $index">
                                <td style="vertical-align: top; width: 150px">@{{ key }}</td>
                                <td>
                                    <span class="text-gray">@{{ data[0]  }}</span>
                                    <span class='text-gray'>⟶</span>
                                    <span>@{{ data[1] }}</span>
                                </td>
                            </tr>
                        </table>
                        {{-- <div ng-repeat="(key, data) in d track by $index" class="log-info">
                            <span>@{{ key }}</span>
                            <span class="text-gray">@{{ data[0] }}</span>
                            <span class='text-gray'>⟶</span>
                            <span>@{{ data[1] }}</span>
                        </div> --}}
                    </td>
                    <td>
                        <span style="white-space: nowrap">@{{ formatDateTime(log.created_at) }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@include('modules.pagination')
@stop
