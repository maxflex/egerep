<div class="row controls-line">
    <div class="col-sm-12">
        <span ng-repeat="request in client.requests"
            ng-click="selectRequest(request)"
            ng-class="{'link-like': request !== selected_request}"><span ng-if='request.id'>заявка @{{ request.id }}</span><span ng-if='!request.id'>новая заявка</span></span>
        <a class='link-like link-gray' ng-click='addRequest()'>добавить</a>
        <a class='link-like text-danger show-on-hover' ng-show='selected_request' ng-click='removeRequest()'>удалить заявку</a>
    </div>
</div>

<div class="row mb">
    <div class="col-sm-6">
        <textarea class="form-control" rows="6" cols="40" placeholder="комментарий" ng-model="selected_request.comment"></textarea>
    </div>
    <div class="col-sm-6">
        <p>
            <b>Заявку создал:</b>
                @{{ getUser(selected_request.user_id_created) ? getUser(selected_request.user_id_created).login : 'system' }}
                @{{ formatDateTime(selected_request.created_at) }}
        </p>
        <p>
            <b>Ответственный:</b>
            <span
                class="link-like"
                ng-click='toggleUser()'
                style='color: @{{ selected_request.user.color }}'>
                @{{ selected_request.user ? selected_request.user.login : 'system' }}
            </span>
        </p>
        <p>
            <b>Статус заявки:</b> <span class="link-like"
                ng-click="toggleEnum(selected_request, 'state', RequestState)">@{{ RequestState[selected_request.state] }}</a>
        </p>
        <p ng-show='tutor_ids.length > 0'>
            <b>Отмеченные репетиторы в заявке:</b>
            <span ng-repeat="tutor_id in tutor_ids" style="display: block">
                <a href="tutors/@{{ tutor_id }}/edit" target="_blank">@{{ tutors[tutor_id] }}</a>
            </span>
        </p>
    </div>
</div>

<div class="row">
    <div class="col-sm-12" ng-if='selected_request.id'>
        <comments entity-type='request' entity-id='selected_request.id' user='{{ $user }}'></comments>
    </div>
</div>
