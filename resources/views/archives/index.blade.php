@extends('app')
@section('title', 'Архивации')
@section('controller', 'ArchivesIndex')

@section('content')

    <div class="row flex-list">
        <div>
            @include('modules.user-select')
        </div>
        <div>
            <select ng-highlight ng-model='search.account_data' class='selectpicker' ng-change='filter()'>
                <option value="" data-subtext="@{{ counts.account_data[''] || '' }}">занятия в отчетности</option>
                <option disabled>──────────────</option>
                <option ng-repeat='(id, name) in Presence[1]'
                        data-subtext="@{{ counts.account_data[id] || '' }}"
                        value="@{{id}}">@{{ name }}</option>
            </select>
        </div>
        <div>
            <select ng-highlight ng-model='search.total_lessons_missing' class='selectpicker' ng-change='filter()'>
                <option value="" data-subtext="@{{ counts.total_lessons_missing[''] || '' }}">занятия к проводке</option>
                <option disabled>──────────────</option>
                <option ng-repeat='(id, name) in Presence[1]'
                        data-subtext="@{{ counts.total_lessons_missing[id] || '' }}"
                        value="@{{id}}">@{{ name }}</option>
            </select>
        </div>
        <div>
            <select ng-highlight ng-model='search.forecast' class='selectpicker' ng-change='filter()'>
                <option value="" data-subtext="@{{ counts.forecast[''] || '' }}">прогноз</option>
                <option disabled>──────────────</option>
                <option ng-repeat='(id, name) in Presence[1]'
                        data-subtext="@{{ counts.forecast[id] || '' }}"
                        value="@{{id}}">@{{ name }}</option>
            </select>
        </div>
        <div>
            <select ng-highlight ng-model='search.debtor' class='selectpicker' ng-change='filter()'>
                <option value="" data-subtext="@{{ counts.debtor[''] || '' }}">вечный должник</option>
                <option disabled>──────────────</option>
                <option ng-repeat='(id, name) in YesNo'
                        data-subtext="@{{ counts.debtor[id] || '' }}"
                        value="@{{id}}">@{{ name }}</option>
            </select>
        </div>
        <div>
            <select ng-highlight ng-model='search.hide' class='selectpicker' ng-change='filter()'>
                <option value="" data-subtext="@{{ counts.hide[''] || '' }}">все</option>
                <option disabled>──────────────</option>
                <option ng-repeat='(id, name) in AttachmentVisibility'
                        data-subtext="@{{ counts.hide[id] || '' }}"
                        value="@{{id}}">@{{ name }}</option>
            </select>
        </div>
        <div>
            <select ng-highlight ng-model='search.grade' class='selectpicker fix-viewport' ng-change='filter()'>
                <option value="" data-subtext="@{{ counts.grade[''] || '' }}">класс</option>
                <option disabled>──────────────</option>
                <option ng-repeat='(id, name) in Grades'
                        data-subtext="@{{ counts.grade[id] || '' }}"
                        value="@{{id}}">@{{ name }}</option>
            </select>
        </div>
        <div>
            <select ng-highlight ng-model='search.error' class='selectpicker fix-viewport' ng-change='filter()'>
                <option value="" data-subtext="@{{ counts.error[''] || '' }}">все</option>
                <option disabled>──────────────</option>
                <option ng-repeat='(id, name) in AttachmentErrors'
                        data-subtext="@{{ counts.error[id] || '' }}"
                        value="@{{id}}">@{{ id }}</option>
            </select>
        </div>
        <div>
            <select ng-highlight ng-model='search.state' class='selectpicker fix-viewport' ng-change='filter()'>
                <option value="" data-subtext="@{{ counts.state[''] || '' }}">разархивация</option>
                <option disabled>──────────────</option>
                <option ng-repeat='(id, name) in ArchiveStates'
                        data-subtext="@{{ counts.state[id] || '' }}"
                        value="@{{id}}">@{{ name }}</option>
            </select>
        </div>
        <div>
            <select ng-highlight ng-model='search.checked' class='selectpicker fix-viewport' ng-change='filter()'>
                <option value="" data-subtext="@{{ counts.checked[''] || '' }}">статус проверки</option>
                <option disabled>──────────────</option>
                <option ng-repeat='(id, name) in Checked'
                        data-subtext="@{{ counts.checked[id] || '' }}"
                        value="@{{id}}">@{{ name }}</option>
            </select>
        </div>
        <div>
            <div class="form-group">
                <div class="input-group custom">
                  <span class="input-group-addon">ID –</span>
                  <input type="text" ng-keyup='keyFilter($event)' class="form-control" ng-model="search.tutor_id">
                </div>
            </div>
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
            <td>Класс</td>
            <td>Ошибки</td>
        </tr>
        </thead>
        <tbody>
        <tr ng-repeat="archive in archives">
            <td align="left" width="5%">
                <a href="requests/@{{ archive.request_id }}/edit#@{{ archive.request_list_id }}#@{{ archive.attachment_id }}">@{{ archive.archive_id }}</a>
            </td>
            <td align="left" width="23%">
                <a href="tutors/@{{ archive.tutor_id }}/edit">@{{ archive.tutor.full_name}}</a>
            </td>
            <td width="6%">
                @{{ formatDate(archive.attachment_date) }}
            </td>
            <td width="6%">
                @{{ archive.lesson_count | hideZero }}<plus previous='archive.lesson_count' count='archive.total_lessons_missing'></plus>
            </td>
            <td width="6%">
                @{{ archive.forecast | hideZero | number}}
            </td>
            <td width='6%'>
                @{{ formatDate(archive.archive_date) }}
            </td>
            <td width='17%'>
                @{{ UserService.getLogin(archive.archive_user_id) }}: @{{ formatDateTime(archive.archive_created_at) }}
            </td>
            <td width='8%'>
                @{{ Grades[archive.grade] }}
            </td>
            <td width='4%'>
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
