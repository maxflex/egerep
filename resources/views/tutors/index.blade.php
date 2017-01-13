@extends('app')
@section('title', 'Преподаватели')
@section('controller', 'TutorsIndex')

@section('title-right')
    {{-- нужно поправить функция link_to_route, чтобы она работала с https --}}
    {{-- {{ link_to_route('tutors.create', 'добавить преподавателя') }} --}}
    <a href="tutors/create">добавить преподавателя</a>

    ошибки обновлены @{{ formatDateTime(tutor_errors_updated) }}
    <span class="glyphicon glyphicon-refresh opacity-pointer" ng-click='recalcTutorErrors()' ng-class="{
        'spinning': tutor_errors_updating == 1
    }"></span>
@stop



@section('content')

<div class="row flex-list">
    <div>
        {{-- <ng-select object='TutorStates' model='state' none-text='статус'></ng-select> --}}
        <select class="form-control" ng-model='state' ng-change="changeState()" id='change-state'>
            <option value="">статус</option>
            <option disabled>──────────────</option>
            <option
                ng-repeat="(state_id, label) in TutorStates"
                data-subtext="@{{ state_counts[state_id] || '' }}"
                value="@{{ state_id }}"
            >
                @{{ label }}
            </option>
        </select>
    </div>
    <div>
        {{-- <ng-select object='TutorStates' model='state' none-text='статус'></ng-select> --}}
        <select class="form-control" ng-model='user_id' ng-change="changeUser()" id='change-user'>
            <option value=''>пользователь</option>
        	<option disabled>──────────────</option>
        	<option
        		ng-repeat="user in UserService.getWithSystem()"
        		ng-show='user_counts[user.id]'
        		value="@{{ user.id }}"
        		data-content="<span style='color: @{{ user.color || 'black' }}'>@{{ user.login }}</span><small class='text-muted'>@{{ user_counts[user.id] || '' }}</small>"
        	></option>
        	<option disabled ng-show="UserService.getBannedHaving(user_counts).length">──────────────</option>
        	<option
        		ng-show='user_counts[user.id]'
                ng-repeat="user in UserService.getBannedUsers()"
        		value="@{{ user.id }}"
        		data-content="<span style='color: black'>@{{ user.login }}</span><small class='text-muted'>@{{ user_counts[user.id] || '' }}</small>"
        	></option>
        </select>
    </div>
    <div>
        <select class="form-control" ng-model='published_state' ng-change="changePublishedSate()" id='change-published'>
            <option value="">все</option>
            <option disabled>──────────────</option>
            <option
                ng-repeat="(state_id, label) in TutorPublishedStates"
                data-subtext="@{{ published_counts[state_id] || '' }}"
                value="@{{ state_id }}"
            >@{{ label }}</option>
        </select>
    </div>
    <div>
        <select class="form-control" ng-model='egecentr_source' ng-change="changeSource()" id='change-source'>
            <option value="">источник</option>
            <option disabled>──────────────</option>
            <option data-subtext="@{{ source_counts[0] || '' }}" value="0">ЕГЭ-Репетитор</option>
            <option data-subtext="@{{ source_counts[1] || '' }}" value="1">ЕГЭ-Центр</option>
        </select>
    </div>
    <div>
        <select class="form-control" ng-model='errors_state' ng-change="changeErrorsState()" id='change-errors'>
            <option value="">все</option>
            <option disabled>──────────────</option>
            <option
                    ng-repeat="(state_id, label) in TutorErrors"
                    data-subtext="@{{ errors_counts[state_id] || '' }}"
                    value="@{{ state_id }}"
            >@{{ state_id }}</option>
        </select>
    </div>
</div>

<table id="tutorList" class="table table-divlike" style="position: relative">
	<tr ng-repeat="tutor in tutors"
		data-id="@{{tutor.id}}">
		<td width='20'><span ng-show="tutor.has_photo_original" class="glyphicon glyphicon-camera" ng-class="{'half-opacity': ! tutor.has_photo_cropped}"></span></td>
		<td style="width:300px"><a href='tutors/@{{ tutor.id }}/edit'>@{{ tutor.full_name }}</a></td>
		<td style="width:100px">
            <span class="label tutor-state-@{{ tutor.state }}">@{{ TutorStates[tutor.state] }}</span>
        </td>
		<td width='75'>
            <span ng-show='tutor.birth_year > 999'>
                @{{ yearDifference(tutor.birth_year) }} <ng-pluralize count="yearDifference(tutor.birth_year)" when="{
                    'one': 'год',
                    'few': 'года',
                    'many': 'лет',
                }"></ng-pluralize>
            </span>
        </td>
        <td width='100'>
            <span ng-show='tutor.clients_count'>
                <plural count='tutor.clients_count' type='client'></plural>
            </span>
        </td>
        <td width='50' class="text-gray">
            <span ng-repeat='phone_field in PhoneFields'>
                <span ng-if="tutor[phone_field + '_duplicate']" ng-click='duplicateClick(tutor[phone_field])'>Д</span>
            </span>
        </td>
		<td style="width:50px">@{{ tutor.tb }}</td>
		<td style="width:50px">@{{ tutor.lk }}</td>
		<td style="width:50px">@{{ tutor.js }}</td>
        <td style="width:100px">
            <user-switch entity='tutor' user-id='responsible_user_id' resource='Tutor'>
		</td>
		<td>
		    <span ng-click="startComment(tutor)" class="glyphicon glyphicon-pencil opacity-pointer" ng-hide="tutor.list_comment || tutor.is_being_commented"></span>
            <input type="text" class='no-border-outline tutor-list-comment' id='list-comment-@{{ tutor.id }}' maxlength="64" placeholder="введите комментарий..."
                ng-model='tutor.list_comment'
                ng-show='tutor.list_comment || tutor.is_being_commented'
                ng-blur='blurComment(tutor)'
                ng-focus='focusComment(tutor)'
                ng-keyup='saveComment($event, tutor)'
            >
		</td>
	</tr>
</table>

<div ng-show="!tutors.length">
    <p align="center" class='text-gray' style="margin: 200px 0">Нет результатов</p>
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
@stop
