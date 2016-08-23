@extends('app')
@section('title', 'Стыковки')
@section('title-right')
    ошибки обновлены @{{ formatDateTime(attachment_errors_updated) }}
    <span class="glyphicon glyphicon-refresh opacity-pointer" ng-click='recalcAttachmentErrors()' ng-class="{
        'spinning': attachment_errors_updating == 1
    }"></span>
@stop
@section('controller', 'AttachmentsStats')

@section('content')

<div class="row">
    <div class="col-sm-2">
        <select ng-model='month' class='sp'>
            <option ng-repeat='(id, name) in Months'
                value="@{{id}}">@{{ name }}</option>
        </select>
    </div>
</div>

<div ng-repeat="year in getYears()">
    <h3 class="center">@{{ year }}</h3>
    <table class="table" style="font-size: 0.8em;">
        <tbody>
            <tr ng-repeat="attachment in data">
            </tr>
        </tbody>
    </table>
</div>
@stop
