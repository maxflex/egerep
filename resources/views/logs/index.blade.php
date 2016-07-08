@extends('app')
@section('title', 'Логи')
@section('controller', 'LogsIndex')

@section('content')

<div class="row flex-list ng-hide">
    <div>
        <select ng-model='search.mode' class='selectpicker' ng-change='filter()'>
            <option value="" data-subtext="@{{ counts.mode[''] || '' }}">все типы отзывов</option>
            <option disabled>──────────────</option>
            <option ng-repeat='(id, name) in Existance'
                data-subtext="@{{ counts.mode[id] || '' }}"
                value="@{{id}}">@{{ name }}</option>
        </select>
    </div>
    <div>
        <select ng-model='search.state' class='selectpicker' ng-change='filter()'>
            <option value="" data-subtext="@{{ counts.state[''] || '' }}">тип публикации</option>
            <option disabled>──────────────</option>
            <option ng-repeat='(id, name) in ReviewStates'
                data-subtext="@{{ counts.state[id] || '' }}"
                value="@{{id}}">@{{ name }}</option>
        </select>
    </div>
    <div>
        <select ng-model='search.signature' class='selectpicker' ng-change='filter()'>
            <option value="" data-subtext="@{{ counts.signature[''] || '' }}">подпись</option>
            <option disabled>──────────────</option>
            <option ng-repeat='(id, name) in Presence[0]'
                data-subtext="@{{ counts.signature[id] || '' }}"
                value="@{{id}}">@{{ name }}</option>
        </select>
    </div>
    <div>
        <select ng-model='search.comment' class='selectpicker' ng-change='filter()'>
            <option value="" data-subtext="@{{ counts.comment[''] || '' }}">текст отзыва</option>
            <option disabled>──────────────</option>
            <option ng-repeat='(id, name) in Presence[0]'
                data-subtext="@{{ counts.comment[id] || '' }}"
                value="@{{id}}">@{{ name }}</option>
        </select>
    </div>
    <div>
        <select ng-model='search.score' class='selectpicker' ng-change='filter()'>
            <option value="" data-subtext="@{{ counts.score[''] || '' }}">все оценки</option>
            <option disabled>──────────────</option>
            <option ng-repeat='(id, name) in ReviewScores'
                data-subtext="@{{ counts.score[id] || '' }}"
                value="@{{id}}">@{{ name }}</option>
        </select>
    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <table class="table reverse-borders">
            <thead>
                <td></td>
                <td></td>
                <td></td>
            </thead>
            <tbody>
                <tr ng-repeat='log in logs'>
                    <td>
                        @{{ log.table }}
                    </td>
                    <td>
                        @{{ log.type }}
                    </td>
                    <td>
                        @{{ log.row_id }}
                    </td>
                    <td>
                        <user model='log.user'></user>
                    </td>
                    <td ng-init='d = toJson(log.data)'>
                        <div ng-repeat="(key, data) in d track by $index" class="log-info">
                            <span>@{{ key }}</span>
                            <span class="text-gray">@{{ data[0] }}</span>
                            <span class='text-gray' ng-show="data[0] != ''">⟶</span>
                            <span>@{{ data[1] }}</span>
                        </div>
                    </td>
                    <td>
                        @{{ formatDateTime(log.created_at) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@include('modules.pagination')
@stop
