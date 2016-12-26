@extends('app')
@section('title', "<a href='tutors/{$tutor->id}/edit'>" . $tutor->getName() . '</a>')
@section('controller', 'AccountsCtrl')

@section('scripts_after')
    <script src="{{ asset('/js/vendor/jquery.caret.js', isProduction()) }}"></script>
@stop

@section('title-right')
    @if(allowed(\Shared\Rights::ER_EDIT_ACCOUNTS))
        <span class="link-like link-reverse link-white"
              ng-if='tutor.last_accounts.length > 0'
              ng-click="save()" ng-disabled="saving"
        >сохранить</span>
    @endif
    <span>вечный должник: <span class='link-white link-reverse link-like' ng-click="toggleEnumServer(tutor, 'debtor', YesNo, Tutor)">@{{ YesNo[tutor.debtor] }}</span></span>
    <span class="link-like link-reverse link-white"
          ng-click='addPlannedAccountDialog();'>
          @{{ tutor.planned_account &&  tutor.planned_account.id ? 'расчет назначен на ' + shortenYear(tutor.planned_account.date) : 'расчет не назначен' }}</span>
    <a href='tutors/{{ $tutor->id }}/accounts/hidden' ng-show="page != 'hidden'" class="client-droppable" style="margin-top: -2px">скрытые ученики (@{{ hidden_clients_count }})</a>
    <a href='tutors/{{ $tutor->id }}/accounts'  ng-show="page == 'hidden'" class="client-droppable" style="margin-top: -2px">показанные в отчетности ученики (@{{ visible_clients_count }})</a>
    @if(allowed(\Shared\Rights::ER_EDIT_ACCOUNTS))
        <span class="link-like link-reverse link-white" ng-click='addAccountDialog()'>добавить расчет</span>
    @endif
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
    @if (allowed(\Shared\Rights::ER_SHOW_TUTOR_DEBT))
        <p class="text-right no-margin-bottom" ng-show='tutor.debt_calc !== null'>Дебет на сегодня: @{{ tutor.debt_calc | number }} руб.</p>
    @endif
    @include('tutors.accounts.partials._fake_table')
    @include('tutors.accounts.partials._real_table')
    @include('tutors.accounts.partials._modals')
@stop
