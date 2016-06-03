<span ng-init='_tutor = {{ $tutor }}'></span>

{{-- <tutor-photo tutor='_tutor' class="ava"></tutor-photo> --}}
<img class='ava' src="@{{ _tutor.photo_url }}">
<div class="info-line">
    <a href="tutors/@{{ _tutor.id }}/edit" target="_blank">@{{ _tutor.full_name }}</a>
</div>
<div class="info-line">
    дебет: @{{ _tutor.debt | number }} руб.
</div>
<div class="info-line">
    дата последнего расчета: @{{ formatDate(_tutor.last_account_info.date_end) }}
</div>
<div class="info-line">
    комментарий: @{{ _tutor.debt_comment | cut:true:25 }}
</div>
