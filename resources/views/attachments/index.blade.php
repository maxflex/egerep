@extends('app')
@section('title', 'Стыковки')
@section('controller', 'AttachmentsIndex')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <ul class="nav nav-tabs nav-tabs-links" style="margin-bottom: 20px">
             <li ng-repeat="(state_id, state) in AttachmentStates" data-id="@{{state_id }}"
                ng-class="{'active' : chosen_state_id == state_id || !chosen_state_id && state_id == 'new', 'request-status-li': status_id != 'all' && (chosen_state_id != status_id)}"
                >
                <a class="list-link" href="#@{{status_id}}" ng-click="changeList(state_id)" data-toggle="tab" aria-expanded="@{{$index == 0}}">
                    @{{ state }}
                </a>
             </li>
        </ul>
    </div>
    <div class="col-sm-12">
        <div ng-repeat="attachment in attachments">
            <a href="requests/@{{ attachment.request_list.request_id }}/edit#@{{ attachment.request_list_id }}#@{{ attachment.id }}">стыковка @{{ attachment.id }}</a>
        </div>
    </div>
    <div class="row" ng-hide="attachments.length">
        <div class="col-sm-12">
            <h3 style="text-align: center; margin: 50px 0">Список стыковок пуст</h3>
        </div>
    </div>
    <pagination style="margin-top: 30px"
            ng-hide='data.last_page <= 1'
            ng-model="current_page"
            ng-change="pageChanged()"
            total-items="data.total"
            max-size="10"
            items-per-page="data.per_page"
            first-text="«"
            last-text="»"
            previous-text="«"
            next-text="»"
        >
    </pagination>
</div>
@stop
