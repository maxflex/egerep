@extends('app')
@section('title', 'Отзывы')
@section('controller', 'ReviewsIndex')

@section('content')

<div class="row flex-list">
    <div>
        <select ng-model='search.mode' class='selectpicker' ng-change='filter()'>
            <option value="">все типы отзывов</option>
            <option disabled>──────────────</option>
            <option ng-repeat='(id, name) in Existance' value="@{{id}}">@{{ name }}</option>
        </select>
    </div>
    <div>
        <select ng-model='search.state' class='selectpicker' ng-change='filter()'>
            <option value="">тип публикации</option>
            <option disabled>──────────────</option>
            <option ng-repeat='(id, name) in ReviewStates' value="@{{id}}">@{{ name }}</option>
        </select>
    </div>
    <div>
        <select ng-model='search.signature' class='selectpicker' ng-change='filter()'>
            <option value="">подпись</option>
            <option disabled>──────────────</option>
            <option ng-repeat='(id, name) in Presence' value="@{{id}}">@{{ name }}</option>
        </select>
    </div>
    <div>
        <select ng-model='search.comment' class='selectpicker' ng-change='filter()'>
            <option value="">текст отзыва</option>
            <option disabled>──────────────</option>
            <option ng-repeat='(id, name) in Presence' value="@{{id}}">@{{ name }}</option>
        </select>
    </div>
    <div>
        <select ng-model='search.score' class='selectpicker' ng-change='filter()'>
            <option value="">все оценки</option>
            <option disabled>──────────────</option>
            <option ng-repeat='(id, name) in ReviewScores' value="@{{id}}">@{{ name }}</option>
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
                        <span ng-show='attachment.review && attachment.review.signature'>есть</span>
                    </td>
                    <td class="center">
                        <span ng-show='attachment.review && attachment.review.comment'>есть</span>
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
