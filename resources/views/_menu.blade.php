<a class="list-group-item active">Основное</a>
<a href="requests" class="list-group-item">Заявки
    <span class="badge pull-right" id='request-count'>{{ \App\Models\Request::where('state', 'new')->count() }}</span>
    <span id='request-counter' class='pull-right' style="margin-right: 3px; opacity: 0; font-size: 13px; font-weight: bold">+1</span>
</a>
<a href="attachments" class="list-group-item">Стыковки
    <span class="badge pull-right" id='attachment-count'>{{ \App\Models\Attachment::countToday() }}</span>
    <span id='attachment-counter' class='pull-right' style="margin-right: 3px; opacity: 0; font-size: 13px; font-weight: bold">+1</span>
</a>
<a href="archives" class="list-group-item">Архивации</a>
<a href="tutors" class="list-group-item">Преподаватели</a>
<a href="reviews" class="list-group-item">Отзывы</a>
<a href="tutors/select" class="list-group-item">Подбор без клиента</a>
<a href="notifications" class="list-group-item">Напоминания
    @if($notifications_count)
        <span class="badge badge-danger pull-right">{{ $notifications_count }}</span>
    @endif
</a>
<a class="list-group-item active">Финансы</a>
@if (\App\Models\User::fromSession()->show_accounts)
    <a href="periods" class="list-group-item">Расчеты</a>
@endif
@if (\App\Models\User::fromSession()->show_debt)
    <a href="debt/map" class="list-group-item">Дебет</a>
@endif
<a class="list-group-item active">Административное</a>
<a href="summary" class="list-group-item">Итоги</a>
<a href="attachments/stats" class="list-group-item">Статистика</a>
<a href="logs" class="list-group-item">Логи</a>
<a href="vocations" class="list-group-item">Отпуски</a>
@if (\App\Models\User::fromSession()->show_contract)
    <a href="contract" class="list-group-item">Договор</a>
@endif
<a href="logout" class="list-group-item">Выход</a>
