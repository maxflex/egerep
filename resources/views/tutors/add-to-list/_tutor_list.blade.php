<td width='300'>
  <a href='tutors/@{{ tutor.id }}/edit' target="_blank">@{{ tutor.full_name }}</a>
</td>
<td width='100'>
  <plural count='tutor.minutes' type='minute'></plural>
</td>
<td>
    <button class="btn btn-mini" style='width: 118px'
        ng-class="{
            'btn-success': !added(tutor.id),
            'btn-danger': added(tutor.id),
        }"
        ng-click='addOrRemove(tutor.id)'
    >
        @{{ added(tutor.id) ? 'убрать из списка' : 'добавить в список' }}
    </button>
</td>
