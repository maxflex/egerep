<div class="row controls-line">
    <div class="col-sm-12">
        <span ng-repeat="request in client.requests"
            ng-click="selectRequest(request)"
            ng-class="{'link-like': request !== selected_request}"><span ng-if='request.id'>заявка @{{ request.id }}</span><span ng-if='!request.id'>новая заявка</span></span>
        <a class='link-like link-gray' ng-show="selected_request.id" ng-click='addRequest()'>добавить</a>
        <div class='controls-right'>
            <a ng-show='selected_request && selected_request.id' ng-click='transferRequest()'>переместить</a>
            @if($user->allowed(\Shared\Rights::ER_DELETE_REQUESTS))
                <a ng-show='selected_request && selected_request.id' ng-click='removeRequest()'>удалить</a>
            @endif
        </div>
    </div>
</div>

<div class="row mb">
    <div class="col-sm-6">
        <textarea class="form-control" rows="6" cols="40" placeholder="комментарий" ng-model="selected_request.comment"></textarea>
    </div>
    <div class="col-sm-6">
        <div class='mbs'>
            <b>Заявку создал:</b>
                @{{ UserService.getLogin(selected_request.user_id_created) }}
                @{{ formatDateTime(selected_request.created_at) }}
        </div>
        <div class='mbs' ng-show='selected_request.id'>
            <b>Ответственный:</b>
            <user-switch entity='selected_request' user-id='user_id' resource='Request'>
        </div>
        <div class='mbs'>
            <b>Статус заявки:</b>
            <span class="link-like"
                ng-click="toggleEnum(selected_request, 'state', RequestStates, ['checked_reasoned_deny'], {{ allowed(\Shared\Rights::ER_REQUEST_STATUSES, true) }})">
                @{{ RequestStates[selected_request.state] }}</span>
        </div>
        <div class='mbs' ng-show='request_tutor_ids.length > 0'>
            <b>Отмеченные репетиторы в заявке:</b>
            <span ng-repeat="tutor_id in request_tutor_ids track by $index" style="display: block">
                <a href="tutors/@{{ tutor_id }}/edit" target="_blank">@{{ tutors[tutor_id] }}</a>
            </span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12" ng-if='selected_request.id'>
        <comments entity-type='request' entity-id='selected_request.id' user='{{ $user }}'></comments>
    </div>
</div>
