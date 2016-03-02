@extends('app')
@section('title', 'Преподаватели')
@section('controller', 'TutorsIndex')

@section('title-right')
    {{ link_to_route('tutors.create', 'добавить преподавателя') }}
@endsection

@section('content')
{{-- <div>
    <div class="row" ng-repeat="tutor in tutors">
        <div class="col-sm-12">
            <a href='tutors/@{{ tutor.id }}/edit'>@{{ tutor.full_name }}</a>
        </div>
    </div>
</div> --}}

<table id="tutorList" class="table table-divlike" style="position: relative">
	<tr ng-repeat="tutor in tutors"
		data-id="@{{tutor.id}}">
		<td width='20'><span ng-show="tutor.has_photo_cropped" class="glyphicon glyphicon-camera"></span></td>
		<td style="width:300px"><a href='tutors/@{{ tutor.id }}/edit'>@{{ tutor.full_name }}</a></td>
		<td style="width:100px">@{{ tutor.approved ? "одобрено" : "с сайта" }}</td>
		<td style="width:100px">
            <span ng-show='tutor.birth_year > 999'>
                @{{ yearDifference(tutor.birth_year) }} <ng-pluralize count="yearDifference(tutor.birth_year)" when="{
                    'one': 'год',
                    'few': 'года',
                    'many': 'лет',
                }"></ng-pluralize>
            </span>
		<td style="width:50px">@{{ tutor.tb }}</td>
		<td style="width:50px">@{{ tutor.lk }}</td>
		<td style="width:50px">@{{ tutor.js }}</td>
        <td style="width:100px">
            <span
                class="link-like"
                ng-model="tutor.responsible_user_id"
                ng-click='toggleResponsibleUser(tutor)'
                style='color: @{{ UserService.getUser(tutor.responsible_user_id, users).color }}'>
                @{{ UserService.getUser(tutor.responsible_user_id, users).login }}
            </span>
		</td>
		<td>
		    <span ng-click="startComment(tutor)" class="glyphicon glyphicon-pencil opacity-pointer" ng-hide="tutor.list_comment || tutor.is_being_commented"></span>
            <input type="text" class='no-border-outline tutor-list-comment' id='list-comment-@{{ tutor.id }}' maxlength="64"
                ng-model='tutor.list_comment'
                ng-show='tutor.list_comment || tutor.is_being_commented'
                ng-blur='blurComment(tutor)'
                ng-focus='focusComment(tutor)'
                ng-keyup='saveComment($event, tutor)'
            >
		</td>
	</tr>
</table>

<pagination style="margin-top: 30px"
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
