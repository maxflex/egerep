<a class="list-group-item active">
    Основное
    <span class="search_icon" id="searchModalOpen"><span class="glyphicon glyphicon-search no-margin-right"></span></span>
</a>
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
    @if(@$notifications_count)
        <span class="badge badge-danger pull-right">{{ $notifications_count }}</span>
    @endif
</a>
<a href="calls/missed" class="list-group-item">Пропущенные вызовы
    @if(@$missed_calls_count)
        <span class="badge badge-danger pull-right">{{ $missed_calls_count }}</span>
    @endif
</a>
@if(allowed(\Shared\Rights::ER_PERIODS) || allowed(\Shared\Rights::ER_PERIODS_PLANNED) || allowed(\Shared\Rights::ER_DEBT))
    <a class="list-group-item active">Финансы</a>
    @if (allowed(\Shared\Rights::ER_PERIODS))
        <a href="periods" class="list-group-item">Совершенные расчеты</a>
    @endif
    @if (allowed(\Shared\Rights::ER_PERIODS_PLANNED))
        <a href="periods/planned" class="list-group-item">Планируемые расчеты</a>
    @endif
    @if (allowed(\Shared\Rights::ER_DEBT))
        <a href="debt/map" class="list-group-item">Дебет</a>
    @endif
@endif
<a class="list-group-item active">Административное</a>
@if (allowed(\Shared\Rights::ER_SUMMARY))
    <a href="summary" class="list-group-item">Итоги</a>
@endif
@if (allowed(\Shared\Rights::ER_ATTACHMENT_STATS))
    <a href="attachments/stats" class="list-group-item">Статистика</a>
@endif
@if (allowed(\Shared\Rights::ER_SUMMARY_USERS))
    <a href="summary/users" class="list-group-item">Эффективность</a>
@endif
@if (allowed(\Shared\Rights::ER_LOGS))
    <a href="logs" class="list-group-item">Логи</a>
@endif
@if (allowed(\Shared\Rights::ER_STREAM))
    <a href="stream" class="list-group-item">Стрим</a>
@endif
@if (allowed(9999))
    <a href="payments" class="list-group-item">Стрим платежей</a>
@endif
@if (allowed(\Shared\Rights::ER_ATTENDANCE))
    <a href="attendance" class="list-group-item">Посещаемость</a>
@endif
@if (allowed(\Shared\Rights::ER_ACTIVITY))
    <a href="activity" class="list-group-item">Активность</a>
@endif
@if (allowed(\Shared\Rights::ER_TEMPLATES))
    <a href="templates" class="list-group-item">Шаблоны</a>
@endif
@if (allowed(\Shared\Rights::SHOW_CONTRACT))
    <a href="contract" class="list-group-item">Договор</a>
@endif
@if (allowed(\Shared\Rights::EMERGENCY_EXIT))
    <a href="emergency" class="list-group-item">Экстренный выход</a>
@endif
<a href="logout" class="list-group-item">Выход</a>
