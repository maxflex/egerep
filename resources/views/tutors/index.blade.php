@extends('app')
@section('title', 'Преподаватели')
@section('controller', 'TutorsIndex')

@section('title-right')
    {{-- нужно поправить функция link_to_route, чтобы она работала с https --}}
    {{-- {{ link_to_route('tutors.create', 'добавить преподавателя') }} --}}
    ошибки обновлены @{{ formatDateTime(tutor_errors_updated) }}
    <span class="glyphicon glyphicon-refresh opacity-pointer" ng-click='recalcTutorErrors()' ng-class="{
        'spinning': tutor_errors_updating == 1
    }"></span>
    <a href="tutors/create">добавить преподавателя</a>
@stop



@section('content')

<div class="row flex-list" style='margin-bottom: 10px'>
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
        <select class="form-control" ng-model='source' ng-change="changeSource()" id='change-source'>
            <option value="">источник</option>
            <option disabled>──────────────</option>
            <option data-subtext="@{{ source_counts[0] || '' }}" value="0">ЕГЭ-Репетитор</option>
            <option data-subtext="@{{ source_counts[1] || '' }}" value="1">ЕГЭ-Центр</option>
            <option data-subtext="@{{ source_counts[2] || '' }}" value="2">HH.ru</option>
        </select>
    </div>
    <div>
        <select class="form-control" ng-model='markers_state' ng-change="changeMarkers()" id='change-markers'>
            <option value="">все</option>
            <option disabled>──────────────</option>
            <option data-subtext="@{{ marker_counts[1] || '' }}" value="1">все метки с адресом</option>
            <option data-subtext="@{{ marker_counts[2] || '' }}" value="2">есть метки без адреса</option>
            <option data-subtext="@{{ marker_counts[3] || '' }}" value="3">нет меток</option>
        </select>
    </div>
    <div>
        <select class="form-control" ng-model='errors_state' ng-change="changeErrorsState()" id='change-errors'>
            <option value="">все</option>
            <option disabled>──────────────</option>
            <option
                    ng-repeat="(id, name) in TutorErrors"
                    data-content="<div title='@{{ name }}'>@{{ id }}<small class='text-muted'>@{{ error_counts[id] || '' }}</small></div>"
                    value="@{{ id }}"
            ></option>
        </select>
    </div>
</div>

<div class="row flex-list" style='width: calc(67% - 8px)'>
    <div>
        <select class="form-control sp" ng-model='in_egecentr' ng-change="changeInEgecentr()"  id='change-in-egecentr'>
            <option value="">статус ЕЦ</option>
            <option disabled>──────────────</option>
            <option
                ng-repeat="(id, label) in Workplaces"
                data-subtext="@{{ in_egecentr_counts[id] || '' }}"
                value="@{{ id }}">@{{ label }}</option>
        </select>
    </div>

    <div>
        <select class="form-control sp" ng-change="changeSubjectsEr()" id='change-subjects-er'
            multiple
            title='Предметы МР'
            ng-model="subjects_er"
        >
             <option
                value="@{{ id }}"
                ng-repeat="(id, subject) in Subjects.all"
                data-subtext="@{{ subjects_er_counts[id] || '' }}">
                @{{ subject }}
            </option>
        </select>
    </div>

    <div>
        <select class="form-control sp" ng-change="changeSubjectsEc()"  id='change-subjects-ec'
            multiple
            title='Предметы ЕЦ'
            ng-model='subjects_ec'
        >
            <option
                value="@{{ id }}"
                ng-repeat="(id, subject) in Subjects.all"
                data-subtext="@{{ subjects_ec_counts[id] || '' }}">
                @{{ subject }}
            </option>
        </select>
    </div>

    <div>
        <select class="form-control sp" ng-model='duplicates' ng-change='changeDuplicates()'>
            <option value="">дубли</option>
            <option disabled>──────────────</option>
            <option value="phone">по номеру телефона</option>
            <option value="last_name">по фамилии</option>
            <option value="last_first_name">по фамилии и имени</option>
            <option value="fio">по фио</option>
        </select>
    </div>
</div>

<table id="tutorList" class="table table-divlike" style="position: relative">
	<tr ng-repeat="tutor in tutors"
		data-id="@{{tutor.id}}">
		<td width='20'>
            <span class="glyphicon glyphicon-camera" ng-show="tutor.has_photo_original" ng-class="{'half-opacity': ! tutor.has_photo_cropped}"></span>
        </td>
		<td style="width:250px">
            <a href='tutors/@{{ tutor.id }}/edit'>@{{ tutor.last_name }} @{{ tutor.first_name[0] }}. @{{ tutor.middle_name[0] }}.</a>
        </td>
        <td ng-if='duplicates' width='150'>
            <span ng-repeat='p in tutor.phones'>
                @{{ p.slice(-3) }}@{{ $last ? '' : ', ' }}
            </span>
        </td>
		<td width='75'>
            <span ng-show='tutor.age' ng-class="{
                'tutor-duplicate-spacer': tutor.hasOwnProperty('duplicate_tutor_ids') && $index > 0
            }">
                @{{ tutor.age }} <ng-pluralize count="tutor.age" when="{
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
        <td style="width:100px">
            <span class="label pointer tutor-state-@{{ tutor.state }} hint--bottom"
                aria-label="статус MR"
                ng-click="toggleEnumServer(tutor, 'state', TutorStates, Tutor)">
                @{{ TutorStates[tutor.state] }}
            </span>
        </td>
        <td style="width:150px">
            <span class="label pointer tutor-inegecentr-@{{ tutor.in_egecentr }}  hint--bottom"
                aria-label="статус EC"
                ng-click="toggleEnumServer(tutor, 'in_egecentr', Workplaces, Tutor)">
                @{{ Workplaces[tutor.in_egecentr] }}
            </span>
        </td>
        <td style="width:100px">
            <user-switch entity='tutor' user-id='responsible_user_id' resource='Tutor'>
		</td>
		<td style='position: relative'>
		    <span ng-click="startComment(tutor)" class="glyphicon glyphicon-pencil opacity-pointer" ng-hide="tutor.list_comment || tutor.is_being_commented"></span>
            <input type="text" class='no-border-outline tutor-list-comment' id='list-comment-@{{ tutor.id }}' maxlength="64" placeholder="введите комментарий..."
                style='font-size: 12px'
                ng-model='tutor.list_comment'
                ng-show='tutor.list_comment || tutor.is_being_commented'
                ng-blur='blurComment(tutor)'
                ng-focus='focusComment(tutor)'
                ng-keyup='saveComment($event, tutor)'
            >
            <span
                ng-show="duplicates && !tutor.clients_count"
                ng-click='deleteTutor($index)'
                class='glyphicon glyphicon-remove text-danger opacity-pointer'
                style="z-index: 1; position: absolute; right: 0; top: @{{ tutor.hasOwnProperty('duplicate_tutor_ids') && $index > 0 ?  '55px': '10px' }}"></span>
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

<style>
.hint--bottom:after {
    width: auto !important;
}
</style>
