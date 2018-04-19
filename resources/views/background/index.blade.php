@extends('app')
@section('title', 'Настройки фона')
@section('controller', 'Background')

@section('content')

<input name="photo" type="file" id="fileupload" data-url="upload/background">

<table class="table reverse-borders backgrounds-table">
    <tr ng-repeat="date in dates">
        <td width='220'>
            @{{ formatDateCustom(date, 'DD MMMM YYYY') }}
        </td>
        <td width='220'>
            <img ng-if="backgrounds[date]" src="@{{ backgrounds[date].image_url }}" />
        </td>
        <td width='150'>
            <span ng-if="!backgrounds[date]" ng-show="date >= today_date" class="link-like" ng-click="loadImage(date)">загрузить</span>

            <span ng-if="backgrounds[date]" ng-show="backgrounds[date].user_id == user.id || {{ allowed(\Shared\Rights::ER_APPROVE_BACKGROUND, true) }}"
                class="link-like" ng-click="remove(date)">удалить</span>
        </td>
        <td width='300'>
            <span ng-if="backgrounds[date]">@{{ backgrounds[date].credentials }}</span>
        </td>
        <td>
            <span class="link-like" ng-if="backgrounds[date]"
                @if(allowed(\Shared\Rights::ER_APPROVE_BACKGROUND))
                    ng-click="toggleEnumServer(backgrounds[date], 'is_approved', UnderModer, Background)"
                @endif
            >
                @{{ UnderModer[backgrounds[date].is_approved] }}
            </span>
        </td>
        <td>
            <a ng-if="backgrounds[date]" ng-show="backgrounds[date].user_id == user.id || {{ allowed(\Shared\Rights::ER_APPROVE_BACKGROUND, true) }}"
                target="_blank" href="background/preview/@{{ backgrounds[date].id }}">предпросмотр</span>
        </td>
    </tr>
</table>
@stop
