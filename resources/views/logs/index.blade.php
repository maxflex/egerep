@extends('app')
@section('title', 'Логи')
@section('controller', 'LogsIndex')

@section('content')

<div class="row flex-list">
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
        <table class="table">
            <thead>
                <td></td>
                <td>ФИО РЕПЕТИТОРА</td>
                <td class="center">СТЫКОВКА</td>
                <td class="center">ЗАНЯТИЙ</td>
                <td class="center">АРХИВАЦИЯ</td>
                <td></td>
                <td class="center">ПОДПИСЬ</td>
                <td class="center">ТЕКСТ ОТЗЫВА</td>
                <td class="center">ОЦЕНКА</td>
            </thead>
            <tbody>
                <tr ng-repeat='attachment in attachments'>
                    <td>
                        <a ng-show='attachment.review' href='@{{ attachment.link }}'>
                            отзыв @{{ attachment.review.id }}
                        </a>
                        <a href='@{{ attachment.link }}' ng-show='!attachment.review' href='' class='text-danger'>
                            создать отзыв
                        </a>
                    </td>
                    <td>
                        <a href='tutors/@{{ attachment.tutor.id }}/edit'>@{{ attachment.tutor.full_name }}</a>
                    </td>
                    <td class="center">
                        @{{ attachment.date }}
                    </td>
                    <td class="center">
                        @{{ attachment.account_data_count | hideZero }}<plus previous='attachment.account_data_count' count='attachment.archive.total_lessons_missing'></plus>
                    </td>
                    <td class="center">
                        @{{ attachment.archive.date }}
                    </td>
                    <td class="center">
                        <span ng-if='attachment.review'>@{{ ReviewStates[attachment.review.state] }}</span>
                    </td>
                    <td class="center">
                        <span ng-show='attachment.review && attachment.review.signature'
                            aria-label='@{{ attachment.review.signature }}' class='cursor-default hint--bottom'>есть</span>
                    </td>
                    <td class="center">
                        <span ng-show='attachment.review && attachment.review.comment'
                            aria-label='@{{ attachment.review.comment }}' class='cursor-default hint--bottom'>есть</span>
                    </td>
                    <td class="center">
                        <span ng-if='attachment.review'>@{{ ReviewScores[attachment.review.score] }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@include('modules.pagination')
@stop
