@extends('app')
@section('title', 'Архивации')
@section('controller', 'ArchivesIndex')

@section('content')

    <div class="row flex-list archive-filters">
        <div>
            <select class="form-control selectpicker" ng-model='search.user_id' ng-change="filter()" id='change-user'>
                <option value="" data-subtext="@{{ counts.user[''] || '' }}">пользователь</option>
                <option disabled>──────────────</option>
                <option
                        ng-repeat="user in UserService.getWithSystem()"
                        value="@{{ user.id }}"
                        data-content="<span style='color: @{{ user.color || 'black' }}'>@{{ user.login }}</span><small class='text-muted'>@{{ counts.user[user.id] || '' }}</small>"
                ></option>
            </select>
        </div>
        <div>
            <select ng-model='search.account_data' class='selectpicker' ng-change='filter()'>
                <option value="" data-subtext="@{{ counts.account_data[''] || '' }}">занятия в отчетности</option>
                <option disabled>──────────────</option>
                <option ng-repeat='(id, name) in Presence[1]'
                        data-subtext="@{{ counts.account_data[id] || '' }}"
                        value="@{{id}}">@{{ name }}</option>
            </select>
        </div>
        <div>
            <select ng-model='search.total_lessons_missing' class='selectpicker' ng-change='filter()'>
                <option value="" data-subtext="@{{ counts.total_lessons_missing[''] || '' }}">занятия к проводке</option>
                <option disabled>──────────────</option>
                <option ng-repeat='(id, name) in Presence[1]'
                        data-subtext="@{{ counts.total_lessons_missing[id] || '' }}"
                        value="@{{id}}">@{{ name }}</option>
            </select>
        </div>
        <div>
            <select ng-model='search.forecast' class='selectpicker' ng-change='filter()'>
                <option value="" data-subtext="@{{ counts.forecast[''] || '' }}">прогноз</option>
                <option disabled>──────────────</option>
                <option ng-repeat='(id, name) in Presence[1]'
                        data-subtext="@{{ counts.forecast[id] || '' }}"
                        value="@{{id}}">@{{ name }}</option>
            </select>
        </div>
        <div>
            <select ng-model='search.debtor' class='selectpicker' ng-change='filter()'>
                <option value="" data-subtext="@{{ counts.debtor[''] || '' }}">вечный должник</option>
                <option disabled>──────────────</option>
                <option ng-repeat='(id, name) in YesNo'
                        data-subtext="@{{ counts.debtor[id] || '' }}"
                        value="@{{id}}">@{{ name }}</option>
            </select>
        </div>
        <div>
            <select ng-model='search.hide' class='selectpicker' ng-change='filter()'>
                <option value="" data-subtext="@{{ counts.hide[''] || '' }}">все</option>
                <option disabled>──────────────</option>
                <option ng-repeat='(id, name) in AttachmentVisibility'
                        data-subtext="@{{ counts.hide[id] || '' }}"
                        value="@{{id}}">@{{ name }}</option>
            </select>
        </div>
        <div>
            <select ng-model='search.error' class='selectpicker fix-viewport' ng-change='filter()'>
                <option value="" data-subtext="@{{ counts.error[''] || '' }}">все</option>
                <option disabled>──────────────</option>
                <option ng-repeat='(id, name) in AttachmentErrors'
                        data-subtext="@{{ counts.error[id] || '' }}"
                        value="@{{id}}">@{{ id }}</option>
            </select>
        </div>
    </div>

    <table class="table" style="font-size: 0.8em;">
        <thead class="bold">
        <tr>
            <td></td>
            <td align="left">
                Преподаватель
            </td>
            <td>
                Cтыковка
            </td>
            <td>
                Занятий
            </td>
            <td>
                Прогноз
            </td>
            <td>
                Архивация
            </td>
            <td>Реквизиты</td>
            <td>Ошибки</td>
        </tr>
        </thead>
        <tbody>
        <tr ng-repeat="archive in archives">
            <td align="left" width="9%">
                <a href="requests/@{{ archive.request_id }}/edit#@{{ archive.request_list_id }}#@{{ archive.attachment_id }}">архивация @{{ archive.archive_id }}</a>
            </td>
            <td align="left" width="23%">
                <a href="tutors/@{{ archive.tutor_id }}/edit">@{{ archive.tutor.full_name}}</a>
            </td>
            <td width="6%">
                @{{ formatDate(archive.attachment_date) }}
            </td>
            <td width="6%">
                @{{ archive.lesson_count | hideZero }}<plus previous='attachment.lesson_count' count='attachment.archive.total_lessons_missing'></plus>
            </td>
            <td width="6%">
                @{{ archive.forecast | hideZero | number}}
            </td>
            <td width='6%'>
                @{{ formatDate(archive.created_at) }}
            </td>
            <td width='20%'>
                @{{ UserService.getLogin(archive.archive_user_id) }}: @{{ formatDateTime(archive.archive_date) }}
            </td>
            <td width='10%'>
                <span ng-repeat='code in archive.errors.split(",")' ng-attr-aria-label="@{{ AttachmentErrors[code] }}" class='hint--bottom-left'>@{{ code }}@{{ $last ? '' : ',  ' }}</span>
            </td>
        </tr>
        </tbody>
    </table>

    <div class="row" ng-hide="archives.length">
        <div class="col-sm-12">
            <h3 style="text-align: center; margin: 50px 0; color: #ddd;">
                <span ng-show="frontend_loading">Загрузка данных...</span>
                <span ng-hide="frontend_loading">Список архиваций пуст</span>
            </h3>
        </div>
    </div>

    <pagination style="margin-top: 30px"
                ng-hide='data.last_page <= 1'
                ng-model="current_page"
                ng-change="pageChanged()"
                total-items="data.total"
                max-size="10"
                items-per-page="data.per_page"
                first-text="«"
                last-text="»"
                previous-text="«"
                next-text="»"
    >
    </pagination>
@stop
