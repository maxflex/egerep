<td width='300'>
  <a href='tutors/@{{ tutor.id }}/edit' target="_blank">@{{ tutor.full_name }}</a>
</td>
<td>
    @{{ tutor.public_price }} руб.
    <span ng-show='tutor.departure_possible'>
        +
        <span ng-show='tutor.departure_price'>выезд от @{{ tutor.departure_price }} руб.</span>
        <span ng-show='!tutor.departure_price'>бесплатный выезд</span>
    </span>
    <span ng-show='!tutor.departure_possible'>(выезд невозможен)</span>
</td>
<td width='150'>
    @include('modules.subjects-list', ['subjects' => 'tutor.subjects', 'type' => 'three_letters'])
</td>
<td width='100'>
    <plural count='tutor.age' type='age'></plural>
</td>
<td width='50'>
    @{{ tutor.review_avg | number:1 }}
</td>
<td width='150'>
    <plural count='tutor.clients_count' type='client' hide-zero></plural>
    @{{ tutor.margin }}
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
