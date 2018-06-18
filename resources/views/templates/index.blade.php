@extends('app')
@section('title', 'Шаблоны')
@section('controller', 'TemplateIndex')

@section('content')
    <div ng-repeat="Template in allTemplates" class="row checkChange" style="margin-bottom: 20px" ng-hide="Template.number == 2 && !{{ allowed(\Shared\Rights::SECRET_SMS, true) }}">
        <div class="col-sm-12">
            <div class="form-group task" style="display: inline-block; width: 100%">
                <b style="margin: 0 0 3px; padding-left: 5px" class="ng-binding">@{{Template.name}}</b>
                <textarea ng-model="Template.text" class="form-control ng-pristine ng-untouched ng-valid" rows="3"></textarea>
                <!--
                <div class="pull-right ng-hide" ng-show="Template.type > 1">
					<span style="margin-right: 8px">
						<input type="checkbox" ng-click="toggle(1, Template)" ng-checked="inWho(1, Template)" ng-true-value="1" ng-false-value="0"> ученикам
					</span>
					<span style="margin-right: 8px">
						<input type="checkbox" ng-click="toggle(2, Template)" ng-checked="inWho(2, Template)" ng-true-value="1" ng-false-value="0"> представителям
					</span>
					<span>
						<input type="checkbox" ng-click="toggle(3, Template)" ng-checked="inWho(3, Template)" ng-true-value="1" ng-false-value="0"> преподавателям
					</span>
                </div>
                -->
            </div>
        </div>
    </div>

    <div class="col-sm-12 center">
        <button class="btn btn-primary disabled" ng-disabled="!form_changed" ng-if="!form_changed">Сохранено</button>
        <button class="btn btn-primary" ng-click="save()"  ng-if="form_changed">Сохранить</button>
    </div>

@stop
