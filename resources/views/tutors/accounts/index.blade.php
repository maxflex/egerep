@extends('app')
@section('title', "<a href='tutors/{$tutor->id}/edit'>" . $tutor->getName() . '</a>')
@section('controller', 'AccountsCtrl')

@section('scripts_after')
    <script src="{{ asset('/js/vendor/jquery.caret.js', isProduction()) }}"></script>
@stop

@section('title-right')
    <a href='tutors/{{ $tutor->id }}/accounts/hidden' class="client-droppable" style="position: absolute; left: 28%; margin-top: -2px">скрытые ученики (@{{ hidden_clients_count }})</a>
    <span style="position: absolute; left: 50%">вечный должник: <span class='link-white link-reverse link-like' ng-click="toggleEnumServer(tutor, 'debtor', YesNo, Tutor)">@{{ YesNo[tutor.debtor] }}</span></span>
    <span ng-show='tutor.debt_calc !== null'>дебет на сегодня: @{{ tutor.debt_calc | number }} руб.</span>
    <span class="link-like link-reverse link-white" ng-click='addAccountDialog()'>добавить расчет</span>
@stop

<style>
.panel-body {
    overflow: hidden;
}
/* Нужно пофиксить этот баг, задача #854.3 */
.input-group-btn button {
    padding: 9px 9.5px !important;
}
</style>

@section('content')
    @include('tutors.accounts.partials._fake_table')
    @include('tutors.accounts.partials._real_table')
    <div class="row" ng-if='tutor.last_accounts.length > 0'>
        <div class="col-sm-12 center">
            <button class="btn btn-primary" ng-click="save()" ng-disabled="saving">Сохранить</button>
        </div>
    </div>

    @include('tutors.accounts.partials._modals')
@stop
