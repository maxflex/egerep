<td width='300'>
  <a href='tutors/@{{ tutor.id }}/edit' target="_blank">@{{ tutor.full_name }}</a>
</td>
<td style="width:100px">
    <span class="label tutor-state-@{{ tutor.state }}">@{{ TutorStates[tutor.state] }}</span>
</td>
<td width='150'>
    @include('modules.subjects-list', ['subjects' => 'tutor.subjects', 'type' => 'three_letters'])
</td>
<td width='100'>
    <plural count='tutor.age' type='age'></plural>
</td>
<td width='50'>
    @{{ tutor.lk }}
</td>
<td width='50'>
    @{{ tutor.tb }}
</td>
<td width='50'>
    @{{ tutor.js }}
</td>
<td width='150'>
    <plural count='tutor.clients_count' type='client' hide-zero></plural>
</td>
<td>
    @{{ tutor.ready_to_work | cut:true:50}}
</td>
<td width='300'>
    <span ng-click="startComment(tutor)" class="glyphicon glyphicon-pencil opacity-pointer" ng-hide="tutor.list_comment || tutor.is_being_commented"></span>
    <input type="text" class='no-border-outline tutor-list-comment' id='list-comment-@{{ tutor.id }}' maxlength="64" placeholder="введите комментарий..."
        ng-model='tutor.list_comment'
        ng-show='tutor.list_comment || tutor.is_being_commented'
        ng-blur='blurComment(tutor)'
        ng-focus='focusComment(tutor)'
        ng-keyup='saveComment($event, tutor)'
    >
</td>
