@extends('app')
@section('title', 'Графы')
@section('controller', 'GraphController')

@section('content')
    <div class="row">
        <div class="col-sm-7">
            <iframe src="svg/map.svg" frameborder="0" marginwidth="0" marginheight="0" data-width="700" id="map"
                scrolling="no" style="height: 900px; width: 100%; overflow: hidden"></iframe>
        </div>
        <div class="col-sm-5">
            <div style="margin: 100px 0 50px; height: 100px">
                <div ng-show='hovered_station_id'>
                    <div ng-repeat="d in found_distances" style='white-space: nowrap'>
                        <span class="place_mark line@{{ findById(stations, d.from).lines }}"></span>
                        @{{ findById(stations, d.from).name }}
                        <span class='glyphicon glyphicon-arrow-right' style='margin-left: 5px; color: rgba(0, 0, 0, 0.5); top: 2px'></span>
                        <span class="place_mark line@{{ findById(stations, d.to).lines }}"></span>
                        @{{ findById(stations, d.to).name }}
                        <span class='glyphicon glyphicon-arrow-right' style='margin-left: 5px; color: rgba(0, 0, 0, 0.5); top: 2px'></span>
                        <b>@{{ d.distance }}</b>
                    </div>
                </div>
            </div>
            <div ng-show='selected &&  selected.length == 2'>
                <div class="center" style="margin-bottom: 10px">
                    <span class="place_mark line@{{ findById(stations, selected[0]).lines }}"></span>
                    @{{ findById(stations, selected[0]).name }}
                    <span class='glyphicon glyphicon-resize-horizontal' style='margin-left: 5px; color: rgba(0, 0, 0, 0.5); top: 2px'></span>
                    <span class="place_mark line@{{ findById(stations, selected[1]).lines }}"></span>
                    @{{ findById(stations, selected[1]).name }}
                </div>
                <div style='width: 30%; margin: 0 auto; margin-top: 20px'>
                    <div class="form-group">
                        <input type="text" class="form-control digits-only" ng-model='new_distance'>
                    </div>
                    <div class="form-group">
                        <button style='width: 100%' class="btn btn-primary" ng-disabled='!new_distance || saving' ng-click='save()'>сохранить</button>
                    </div>
                    <div class="form-group">
                        <button style='width: 100%' class="btn btn-danger" ng-disabled='!new_distance || saving'  ng-click='delete()'>удалить</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
