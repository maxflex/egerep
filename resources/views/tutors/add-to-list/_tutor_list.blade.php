<td width='300'>
  <a href='tutors/@{{ tutor.id }}/edit' target="_blank">@{{ tutor.full_name }}</a>
</td>
<td width='150'>
    @include('modules.subjects-list', ['subjects' => 'tutor.subjects', 'type' => 'three_letters'])
</td>
<td width='100'>
    <plural count='tutor.age' type='age'></plural>
</td>
<td width='50'>
    @{{ tutor.tb }}
</td>
<td width='50'>
    @{{ tutor.lk }}
</td>
<td width='50'>
    @{{ tutor.js }}
</td>
<td width='150'>
    <plural count='tutor.clients_count' type='client' hide-zero></plural>
</td>
<td width='150'>
  <plural count='getHours(tutor.minutes)' type='hour' hide-zero></plural>
  <plural count='getMinutes(tutor.minutes)' type='minute'></plural>
</td>
<td>
    <span class="link-like text-gray" style='width: 118px'
        ng-class="{
            'text-danger': added(tutor.id),
        }"
        ng-click='addOrRemove(tutor.id)'
    >
        @{{ added(tutor.id) ? 'убрать из списка' : 'добавить в список' }}
    </button>
</td>
