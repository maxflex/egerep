@extends('app')
@section('title', 'Заявки')
@section('controller', 'RequestsIndex')

@section('title-right')
    {{ link_to_route('requests.create', 'добавить заявку') }}
@endsection

@section('content')
<sms number='sms_number'></sms>

<div class="row">
    <div class="col-sm-10" style="width: 80%">
        <ul class="nav nav-tabs nav-tabs-links request-links" style="margin: 7px 0 40px">
             <li ng-repeat="(state_id, state) in RequestStates" data-id="@{{state_id }}"
                ng-class="{'active' : chosen_state_id == state_id || !chosen_state_id && state_id == 'new', 'request-status-li': status_id != 'all' && (chosen_state_id != status_id)}"
                >
                <a class="list-link" href="#@{{status_id}}" ng-click="changeList(state_id)" data-toggle="tab" aria-expanded="@{{$index == 0}}">
                    @{{ state }}
                </a>
                (@{{ request_state_counts[state_id] }})
             </li>
        </ul>
    </div>
    <div class="col-sm-2" style="width: 20%">
        <select class="form-control" ng-model='user_id' ng-change="changeUser()" id='change-user'>
           <option value="">пользователь</option>
           <option disabled>──────────────</option>
           <option
               ng-show='user_counts[user.id]'
               ng-repeat="user in UserService.getWithSystem()"
               value="@{{ user.id }}"
               data-content="<span style='color: @{{ user.color || 'black' }}'>@{{ user.login }} @{{ $var }}</span><small class='text-muted'>@{{ user_counts[user.id] || '' }}</small>"
           ></option>
           <option>──────────────</option>
           <option
                   ng-show='user_counts[user.id]'
                   ng-repeat="user in UserService.getBannedUsers()"
                   value="@{{ user.id }}"
                   data-content="<span style='color: @{{ user.color || 'black' }}'>@{{ user.login }} @{{ $var }}</span><small class='text-muted'>@{{ user_counts[user.id] || '' }}</small>"
           ></option>
       </select>
    </div>
</div>

<div>
    <div class="row request-main-list"
         ng-repeat="request in requests"
         data-id="@{{request.id}}"
         ng-class="{ 'manual-request-red': request.contract_time && request.contract_time > 0 && request.contract_time <= 3600 }">
         <div class="col-sm-12">
            <div>
                <metro-list style='top: -2px; position: relative' markers='request.client.markers' inline one_station='true'></metro-list>
                <b ng-show="request.client.address">@{{request.client.address}}</b>
                <b ng-show="!request.client.address">описание отсутствует</b>
                <b ng-show="!request.client.phones.length">телефон отсутствует</b>
                <b ng-repeat="phone_field in ['phone', 'phone2', 'phone3', 'phone4']">
                    <span ng-show="request.client[phone_field]">
                        <span class="underline-hover inline-block"
                              ng-click="PhoneService.call(request.client[phone_field])"
                              ng-class="{'phone-duplicate-new': request.client[phone_field + '_duplicate']}"
                        >
                              @{{ PhoneService.format(request.client[phone_field]) }}</span>
                    </span>
                </b>
                <span ng-show="request.client.requests_count > 1"><i class="fa fa-circle"></i> <plural type='request' count='request.client.requests_count'></plural></span>
                <span><i class="fa fa-circle"></i>ответственный:
                <user-switch entity='request' user-id='user_id' resource='Request'></span>
            </div>
            <div class="row">
                <div class="col-sm-10 vcenter" style="width: 80%">
                    <div style="margin-top: 10px">
                        <a class="link-reverse" href="requests/@{{request.id}}/edit">Заявка @{{request.id}}</a>:
                        <span class="angular-with-newlines">@{{ request.comment }}</span>
                    </div>
                    <div style="margin-top: 10px" ng-show="request.lists.length">
                        Созданные списки: <span
                            ng-repeat="list in request.lists"
                        ><a class="link-reverse" href='requests/@{{request.id}}/edit#@{{ list.id }}'><sbj ng-repeat='subject_id in list.subjects'>@{{Subjects.all[subject_id]}}@{{$last ? '' : ' и '}}</sbj></a>@{{ $last ? '' : ', ' }}</span>
                    </div>
                </div>
                <div class="col-sm-2 vcenter" style="width: 19%; text-align: right; font-size: 24px" ng-init="how_long_ago = howLongAgo(request.created_at)">
                    <span ng-show="!how_long_ago.days && !how_long_ago.hours">только что</span>
                    <span ng-show="how_long_ago.days">
                        <plural count='how_long_ago.days' type='day'></plural>
                    </span>
                    <span ng-show="how_long_ago.hours">
                        <plural ng-show='how_long_ago.hours' count='how_long_ago.hours' type='hour'></plural>
                    </span>
                </div>
            </div>
            <div style="margin-top: 10px">
                <comments entity-type='request' entity-id='request.id' user='{{ $user }}' track-loading='1'></comments>
            </div>
            {{-- <div class="row" style="margin-top: 20px">
                <div class="col-sm-6">
                    <div class="half-black">
                        Заявка №@{{request.id}} создана @{{UserService.getLogin(request.user_id_created)}} @{{request.id_user_created}}
                        @{{ formatDateTime(request.created_at) }}
                    </div>
                </div>
                <div class="col-sm-4">
                </div>
                <div class="col-sm-2">
                    <span class="link-like" ng-click="toggleState(request)">@{{ RequestStates[request.state] }}</span>
                </div>
            </div> --}}
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
