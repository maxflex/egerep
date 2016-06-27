<a class="list-group-item active">Меню</a>
<a href="requests" class="list-group-item">Заявки</a>
<a href="attachments" class="list-group-item">Стыковки</a>
<a href="tutors" class="list-group-item">Преподаватели</a>
@if (\App\Models\User::fromSession()->show_accounts)
    <a href="periods" class="list-group-item">Расчеты</a>
@endif

@if (\App\Models\User::fromSession()->show_debt)
    <a href="debt/map" class="list-group-item">Дебет</a>
@endif
<a href="reviews" class="list-group-item">Отзывы</a>
<a href="summary" class="list-group-item">Итоги</a>
<a href="logout" class="list-group-item">Выход</a>
