<span ng-init='_tutor = {{ $tutor }}'></span>

{{-- <tutor-photo tutor='_tutor' class="ava"></tutor-photo> --}}
<img class='ava' src="img/tutors/@{{ _tutor.has_photo_cropped ? _tutor.id + '@2x.' + _tutor.photo_extension : 'no-profile-img.gif' }}">
<div class="info-line">
    <a href="tutors/@{{ _tutor.id }}/edit" target="_blank">@{{ _tutor.full_name }}</a>
    <span ng-repeat='phone in _tutor.phones track by $index' title="@{{phone}}" ng-click='PhoneService.call(phone)'
        class="opacity-pointer glyphicon glyphicon-earphone pull-right small"></span>
</div>
<div class="info-line">
    <plural count='_tutor.age' type='age'></plural>
    <span class='remove-space' ng-show='_tutor.public_price > 0'>, @{{ _tutor.public_price }} р.</span>
    <span ng-show='_tutor.departure_price > 0'>+ выезд от @{{ _tutor.departure_price }} р.</span>
</div>
<div class="info-line">
    <plural count='_tutor.clients_count' type='student' none-text='учеников нет'></plural>
    <span class='remove-space'>
        , <plural count='_tutor.meeting_count' type='meeting' none-text='встреч нет'></plural>
    </span>
</div>
<div class="info-line">
    дальность: <plural count='_tutor.minutes' type='minute'></plural>
    <button class="btn btn-mini pull-right" style='width: 118px'
        ng-class="{
            'btn-success': !added(_tutor.id),
            'btn-danger': added(_tutor.id),
        }"
        ng-click='addOrRemove(_tutor.id)'
    >
        @{{ added(_tutor.id) ? 'убрать из списка' : 'добавить в список' }}
    </button>
</div>
