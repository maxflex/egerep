@extends('app')
@section('title', 'Заявки')
@section('controller', 'RequestsIndex')

@section('title-right')
    {{ link_to_route('requests.create', 'добавить заявку') }}
@endsection

@section('content')
<sms number='sms_number'></sms>

<div class="row mb">
    <div class="col-sm-3">
        {{-- <ng-select object='TutorStates' model='state' none-text='статус'></ng-select> --}}
        <select class="form-control" ng-model='state' ng-change="changeState()" id='change-state'>
            <option value="all">все</option>
            <option disabled>──────────────</option>
            <option
                ng-repeat="(state_id, state) in RequestStates"
                data-subtext="@{{ request_state_counts[state_id] || '' }}"
                value="@{{ state_id }}"
            >
                @{{ state }}
            </option>
        </select>
    </div>
    <div class="col-sm-3">
        {{-- <ng-select object='TutorStates' model='state' none-text='статус'></ng-select> --}}
        <select class="form-control" ng-model='user_id' ng-change="changeUser()" id='change-user'>
            <option value="">пользователь</option>
            <option disabled>──────────────</option>
            <option
                ng-repeat="user in UserService.getWithSystem()"
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
                <span ng-show="request.comment" style="margin-right: 10px">@{{request.comment}}</span>
                <span class="half-black">
                    <span ng-show="request.client.name">@{{request.client.name}},</span>
                    <span ng-show="request.client.grade > 0">@{{ Grades[request.client.grade] }},</span>
                    <span ng-show="request.client.address">@{{ request.client.address }}</span>
                    <span ng-repeat="phone_field in ['phone', 'phone2', 'phone3', 'phone4']">
                        <span ng-show="request.client[phone_field]">
                            <span class="underline-hover inline-block"
                                  ng-click="PhoneService.call(request.client[phone_field])"
                                  ng-class="{'phone-duplicate-new': request.client[phone_field + '_duplicate']}"
                            >
                                  @{{ PhoneService.format(request.client[phone_field]) }}</span>

                            <span class="glyphicon glyphicon-envelope sms-in-list"
                                  ng-click="PhoneService.sms(request.client[phone_field])"
                                  ng-show="PhoneService.isMobile(PhoneService.format(request.client[phone_field]))"></span>
                        </span>
                    </span>
                </span>
            </div>

            <div style="margin-top: 10px">
                <comments entity-type='request' entity-id='request.id' user='{{ $user }}' track-loading='1'></comments>
            </div>
            <div class="row" style="margin-top: 20px">
                <div class="col-sm-6">
                    <div class="half-black">
                        Заявка №@{{request.id}} создана @{{UserService.getLogin(request.user_id_created)}} @{{request.id_user_created}}
                        {{-- @todo --}}
                        @{{ formatDateTime(request.created_at) }}
                        <a class="link-reverse" style="margin-left: 5px" href="requests/@{{request.id}}/edit">редактировать</a>
                    </div>
                </div>
                <div class="col-sm-4">
                    ответственный:
                    <user-switch entity='request' user-id='user_id' resource='Request'>
                </div>
                <div class="col-sm-2">
                    <span class="link-like" ng-click="toggleState(request)">@{{ RequestStates[request.state] }}</span>
                </div>
            </div>
            <hr class='list-separate' ng-hide="$last">
        </div>
    </div>


    <div class="row" ng-hide="requests.length">
        <div class="col-sm-12">
            <h3 style="text-align: center; margin: 50px 0">Список заявок пуст</h3>
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
