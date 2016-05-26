@extends('app')
@section('title', 'Отчетность репетитора ' . $tutor->getName())
@section('controller', 'AccountsCtrl')

@section('scripts_after')
    <script src="{{ asset('/js/vendor/jquery.caret.js', isProduction()) }}"></script>
@stop

@section('title-right')
    <a href='tutors/{{ $tutor->id }}/accounts/hidden' class="client-droppable">скрытые ученики (@{{ hidden_clients_count }})</a>
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
