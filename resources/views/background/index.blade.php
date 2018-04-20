@extends('app')
@section('title', 'Настройки фона')
@section('controller', 'Background')

@section('content')

<input name="photo" type="file" id="fileupload" data-url="upload/background" style='display: none'>

<table class="table reverse-borders backgrounds-table">
    <tr ng-repeat-start="date in dates"
        ng-class="{'no-border-bottom': backgrounds[date] && (backgrounds[date].user_id == user.id || {{ allowed(\Shared\Rights::ER_APPROVE_BACKGROUND, true) }})}">
        <td width='220'>
            @{{ formatDateCustom(date, 'DD MMMM YYYY') }}
        </td>
        <td width='220'>
            <div ng-if="backgrounds[date]">
                <img ng-show="backgrounds[date].user_id == user.id || {{ allowed(\Shared\Rights::ER_APPROVE_BACKGROUND, true) }}" src="@{{ backgrounds[date].image_url }}" />
                <img ng-hide="backgrounds[date].user_id == user.id || {{ allowed(\Shared\Rights::ER_APPROVE_BACKGROUND, true) }}" src="/img/icons/no-image.png" />
            </div>
        </td>
        <td width='150'>
            <span ng-if="!backgrounds[date]" ng-show="date >= today_date" class="link-like" ng-click="loadImage(date)">загрузить</span>

            <span ng-if="backgrounds[date]" ng-show="backgrounds[date].user_id == user.id || {{ allowed(\Shared\Rights::ER_APPROVE_BACKGROUND, true) }}"
                class="link-like" ng-click="remove(date)">удалить</span>
        </td>
        <td width='300'>
            <span ng-if="backgrounds[date]">@{{ backgrounds[date].credentials }}</span>
        </td>
        <td width='220'>
            <span class="link-like" ng-if="backgrounds[date]" ng-class="{'text-danger': backgrounds[date].status == 2}"
                @if(allowed(\Shared\Rights::ER_APPROVE_BACKGROUND))
                    ng-click="toggleEnumServer(backgrounds[date], 'status', UnderModer, Background)"
                @endif
            >
                @{{ UnderModer[backgrounds[date].status] }}
            </span>
        </td>
        <td>
            <a ng-if="backgrounds[date]" ng-show="backgrounds[date].user_id == user.id || {{ allowed(\Shared\Rights::ER_APPROVE_BACKGROUND, true) }}"
                target="_blank" href="background/preview/@{{ backgrounds[date].id }}">предпросмотр</span>
        </td>
    </tr>
    <tr ng-repeat-end ng-show="backgrounds[date] && (backgrounds[date].user_id == user.id || {{ allowed(\Shared\Rights::ER_APPROVE_BACKGROUND, true) }})">
        <td colspan="6" style='padding-top: 16px'>
            <comments ng-if="backgrounds[date]" entity-type='background' entity-id='backgrounds[date].id' user='{{ $user }}'></comments>
        </td>
    </tr>
</table>
@stop
