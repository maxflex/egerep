@extends('app')
@section('title', 'Скрытые ученики репетитора ' . $tutor->getName())
@section('controller', 'AccountsHiddenCtrl')

@section('title-right')
    <a href='tutors/{{ $tutor->id }}/accounts' class="client-droppable">показанные в отчетности ученики (@{{ visible_clients_count }})</a>
@stop

<style>
/* Нужно пофиксить этот баг, задача #854.3 */
.input-group-btn button {
    padding: 9px 9.5px !important;
}
</style>

@section('content')
    <table class="table">
        <thead class="bold">
            <tr>
                <td>ИМЯ УЧЕНИКА</td>
                <td>КЛАСС</td>
                <td>ДАТА СТЫКОВКИ</td>
                <td>ДАТА АРХИВАЦИИ</td>
                <td>ВСЕГО ЗАНЯТИЙ</td>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat='client in clients'>
                <td class="client-draggable" data-id='@{{ client.id }}'>
                    <a href='@{{ client.link }}'>
                        <span ng-show='client.name'>@{{ client.name }}</span>
                        <span ng-hide='client.name'>имя не указано</span>
                    </a>
                </td>
                <td>@{{ Grades[client.grade] }}</td>
                <td>@{{ formatDate(client.attachment_date) }}</td>
                <td>@{{ formatDate(client.archive_date) }}</td>
                <td>@{{ client.lessons_count }}</td>
            </tr>
        </tbody>
    </table>
@stop
