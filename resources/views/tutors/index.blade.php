@extends('app')
@section('title', 'Преподаватели')
@section('controller', 'TutorsIndex')

@section('title-right')
    {{ link_to_route('tutors.create', 'добавить преподавателя') }}
@endsection

@section('content')

<div class="row mb">
    <div class="col-sm-3">
        {{-- <ng-select object='TutorStates' model='state' none-text='статус'></ng-select> --}}
        <select class="form-control" ng-model='state' ng-change="changeState()">
            <option value="">статус</option>
            <option disabled>──────────────</option>
            <option
                ng-repeat="(state_id, label) in TutorStates"
                value="@{{ state_id }}"
            >@{{ label }} @{{ state_counts[state_id] > 0 ? '(' + state_counts[state_id] + ')' : '' }}</option>
        </select>
    </div>
    <div class="col-sm-3">
        {{-- <ng-select object='TutorStates' model='state' none-text='статус'></ng-select> --}}
        <select class="form-control" ng-model='user_id' ng-change="changeUser()">
            <option value="">пользователь</option>
            <option disabled>──────────────</option>
            <option
                ng-repeat="user in UserService.users"
                value="@{{ user.id }}"
            >@{{ user.login }} @{{ user_counts[user.id] > 0 ? '(' + user_counts[user.id] + ')' : '' }}</option>
        </select>
    </div>
</div>

<table id="tutorList" class="table table-divlike" style="position: relative">
	<tr ng-repeat="tutor in tutors"
		data-id="@{{tutor.id}}">
		<td width='20'><span ng-show="tutor.has_photo_cropped" class="glyphicon glyphicon-camera"></span></td>
		<td style="width:300px"><a href='tutors/@{{ tutor.id }}/edit'>@{{ tutor.full_name }}</a></td>
		<td style="width:100px">
            <span class="label tutor-state-@{{ tutor.state }}">@{{ TutorStates[tutor.state] }}</span>
        </td>
		<td style="width:100px">
            <span ng-show='tutor.birth_year > 999'>
                @{{ yearDifference(tutor.birth_year) }} <ng-pluralize count="yearDifference(tutor.birth_year)" when="{
                    'one': 'год',
                    'few': 'года',
                    'many': 'лет',
                }"></ng-pluralize>
            </span>
        </td>
        <td width='50' class="text-danger bold">@{{ tutor.has_clients ? 'K' : '' }}</td>
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
    ng-show="tutors.length"
>
</pagination>
@stop
