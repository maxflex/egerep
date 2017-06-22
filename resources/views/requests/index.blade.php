@extends('app')
@section('title', 'Заявки')
@section('controller', 'RequestsIndex')

@section('title-right')
    <span ng-show='errors' style="margin-right: 0">
        ошибки обновлены @{{ formatDateTime(request_errors_updated) }}
        <span class="glyphicon glyphicon-refresh opacity-pointer" style='margin-right: 0' ng-click='recalcErrors()' ng-class="{
            'spinning': request_errors_updating == 1
        }"></span>
    </span>
    <span ng-show='!errors'>
        @if(allowed(\Shared\Rights::ER_REQUEST_ERRORS))
            <a href='requests/errors'>ошибки</a>
        @endif
        {{ link_to_route('requests.create', 'добавить заявку') }}
    </span>
@endsection

@section('content')
<sms number='sms_number'></sms>

<div class="row">
    <div class="col-sm-10" style='width: 80%' ng-show='!errors'>
        <ul class="nav nav-tabs nav-tabs-links request-links" style="margin-top: 7px">
             <li ng-repeat="(state_id, state) in RequestStates" data-id="@{{state_id }}"
                ng-show="['reasoned_deny', 'deny', 'checked_reasoned_deny'].indexOf(state_id) === -1"
                ng-class="{'active' : chosen_state_id == state_id || !chosen_state_id && state_id == 'new' || (['reasoned_deny', 'deny', 'checked_reasoned_deny'].indexOf(chosen_state_id) !== -1 && state_id == 'all_denies'), 'request-status-li': status_id != 'all' && (chosen_state_id != status_id)}"
                >
                <a class="list-link" href="#@{{status_id}}" ng-click="changeList(state_id)" data-toggle="tab" aria-expanded="@{{$index == 0}}">
                    @{{ state }}
                </a>
                <span class='small-count'>@{{ request_state_counts[state_id] }}</span>
             </li>
        </ul>
        <ul class="nav nav-tabs nav-tabs-links request-links" ng-show="(chosen_state_id == 'all_denies') || ['reasoned_deny', 'deny', 'checked_reasoned_deny'].indexOf(chosen_state_id) !== -1">
             <li ng-repeat="(state_id, state) in RequestStates" data-id="@{{state_id }}"
                ng-show="['reasoned_deny', 'deny', 'checked_reasoned_deny'].indexOf(state_id) !== -1"
                ng-class="{'active' : chosen_state_id == state_id || !chosen_state_id && state_id == 'new', 'request-status-li': status_id != 'all' && (chosen_state_id != status_id)}"
                >
                <a class="list-link" href="#@{{status_id}}" ng-click="changeList(state_id)" data-toggle="tab" aria-expanded="@{{$index == 0}}">
                    @{{ state }}
                </a>
                <span class='small-count'>@{{ request_state_counts[state_id] }}</span>
             </li>
        </ul>
    </div>
    <div ng-show='errors' class="col-sm-2" style="width: 20%; padding-right: 0">
        <div ng-show="chosen_state_id == 'all'">
            <select  ng-model='error' class='selectpicker' ng-change='changeUser()' id='error-counts'>
                <option value="" data-subtext="@{{ error_counts[''] || '' }}">все</option>
                <option disabled>──────────────</option>
                <option ng-repeat='(id, name) in RequestErrors' data-title='test'
                    data-content="<div title='@{{ name }}'>@{{ id }}<small class='text-muted'>@{{ error_counts[id] || '' }}</small></div>"
                    value="@{{id}}"></option>
            </select>
        </div>
    </div>
    <div class="col-sm-2" style='width: 20%' ng-show='!errors'>
        <select class="form-control" ng-model='user_id' ng-change="changeUser()" id='change-user'>
           <option value="">пользователь</option>
           <option disabled>──────────────</option>
           <option
               ng-show='user_counts[user.id]'
               ng-repeat="user in UserService.getWithSystem()"
               value="@{{ user.id }}"
               data-content="<span style='color: @{{ user.color || 'black' }}'>@{{ user.login }} @{{ $var }}</span><small class='text-muted'>@{{ user_counts[user.id] || '' }}</small>"
           ></option>
           <option ng-show="hasBannedUsers()" disabled>──────────────</option>
           <option
                   ng-show='user_counts[user.id]'
                   ng-repeat="user in UserService.getBannedUsers()"
                   value="@{{ user.id }}"
                   data-content="<span style='color: @{{ user.color || 'black' }}'>@{{ user.login }} @{{ $var }}</span><small class='text-muted'>@{{ user_counts[user.id] || '' }}</small>"
           ></option>
       </select>
    </div>
</div>

<div style='margin-top: 40px'>
    <div class='row'>
        <div class='col-sm-12'>
            <div class="request-main-list"
                 ng-repeat="request in requests"
                 data-id="@{{request.id}}">
                 <div class="request-left-info">
                     <div>
                         <div>
                             Ответственный
                         </div>
                         <div>
                             <user-switch entity='request' user-id='user_id' resource='Request'></span>
                         </div>
                     </div>
                     <div>
                         <div>
                             Клиент
                         </div>
                         <div>
                             <div style='margin-bottom: 20px'>
                                 <span ng-if='request.client.markers.length'>
                                     <metro-list-full markers='request.client.markers'></metro-list-full>
                                 </span>
                                 <span ng-if='!request.client.markers.length'>метки не установлены</span>
                            </div>
                             <div style='margin-bottom: 20px'>
                                 <span ng-if='request.client.address'>
                                     @{{ request.client.address }}
                                 </span>
                                 <span ng-if='!request.client.address'>
                                     адрес не заполнен
                                 </span>
                             </div>
                             <div ng-show="request.client.phones.length">
                                 <span ng-repeat="phone_field in ['phone', 'phone2', 'phone3', 'phone4']">
                                     <span ng-show="request.client[phone_field]" style='margin-right: 25px'>
                                         <a class='pointer'
                                               ng-click="PhoneService.call(request.client[phone_field])"
                                               ng-class="{'phone-duplicate-new': request.client[phone_field + '_duplicate']}"
                                         >
                                               @{{ PhoneService.format(request.client[phone_field]) }}</a>
                                        <span class='phone-hint' ng-show="request.client[phone_field + '_comment']">@{{ request.client[phone_field + '_comment'] }}</span>
                                     </span>
                                 </span>
                             </div>
                             <div ng-if="!request.client.phones.length">
                                 телефон не указан
                             </div>
                         </div>
                     </div>
                     <div>
                         <div>
                            <a href="requests/@{{request.id}}/edit">Заявка</a>
                            <span class='text-gray' ng-show='request.number > 1'>@{{ request.number }}-я</span>
                         </div>
                         <div>
                             <span class="angular-with-newlines">@{{ request.comment }}</span>
                         </div>
                     </div>
                     <div ng-show="request.lists.length">
                         <div>
                             Списки
                         </div>
                         <div>
                             <span style='margin-right: 25px'
                                 ng-repeat="list in request.lists"
                             ><a href='requests/@{{request.id}}/edit#@{{ list.id }}'><sbj ng-repeat='subject_id in list.subjects'>@{{Subjects.all[subject_id]}}@{{$last ? '' : ' и '}}</sbj></a></span>
                         </div>
                     </div>
                     <div style="margin-bottom: 12px">
                         <comments entity-type='request' entity-id='request.id' user='{{ $user }}' track-loading='1'></comments>
                     </div>
                 </div>
                 <div class="request-right-info" ng-init="how_long_ago = howLongAgo(request.created_at)">
                     <span ng-show="!how_long_ago.days && !how_long_ago.hours && how_long_ago.minutes">
                         <plural count='how_long_ago.minutes' type='minute'></plural>
                     </span>
                     <span ng-show="(how_long_ago.days < 2) && how_long_ago.hours">
                         <plural ng-show='how_long_ago.hours' count='how_long_ago.hours' type='hour'></plural>
                     </span>
                     <span ng-show="how_long_ago.days && how_long_ago.days >= 2">
                         <plural count='how_long_ago.days' type='day'></plural>
                     </span>
                 </div>
            </div>
        </div>
    </div>


    <div class="row" ng-hide="requests.length">
        <div class="col-sm-12">
            <h3 style="text-align: center; margin: 220px 0">список заявок пуст</h3>
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
