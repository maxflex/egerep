@extends('app')
@section('title', 'Заявки')
@section('controller', 'RequestsIndex')

@section('title-right')
    {{ link_to_route('requests.create', 'добавить заявку') }}
@endsection

@section('content')
<sms number='sms_number'></sms>
<div class="row">
    <div class="col-sm-12">
        <ul class="nav nav-tabs nav-tabs-links" style="margin-bottom: 20px">
             <li ng-repeat="(state_id, state) in RequestStates" data-id="@{{state_id }}"
                ng-class="{'active' : chosen_state_id == state_id || !chosen_state_id && state_id == 'new', 'request-status-li': status_id != 'all' && (chosen_state_id != status_id)}"
                >
                <a class="list-link" href="#@{{status_id}}" ng-click="changeList(state_id)" data-toggle="tab" aria-expanded="@{{$index == 0}}">
                    @{{ state }} (@{{ request_state_counts[state_id] }})
                </a>
             </li>
        </ul>
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
                    <span ng-show="request.client.address">@{{ request.client.  address }}</span>
                    <span ng-repeat="phone_field in ['phone', 'phone2', 'phone3']">
                        <span ng-show="request.client[phone_field]">
                            <span class="underline-hover inline-block"
                                  ng-click="PhoneService.call(request.client[phone_field])">
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
                <div class="col-sm-6">
                    ответственный:
                    <user-switch entity='request' user-id='user_id' resource='Request'>
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
