@extends('app')
@section('title', 'Дебет')
@section('controller', 'DebtIndex')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <table class="table">
            <thead class="bold">
                <tr>
                    <td>ПРЕПОДАВАТЕЛЬ</td>
                    <td>ПОСЛЕДНЯЯ ЗАДОЛЖЕННОСТЬ</td>
                    <td>ДЕБЕТ</td>
                    <td>КОММЕНТАРИЙ</td>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="tutor in tutors">
                    <td>
                        <a href='tutors/@{{ tutor.id }}/edit'>@{{ tutor.full_name }}</a>
                    </td>
                    <td>
                        <span ng-class="{
                            'text-danger': tutor.last_debt.debt_type == 0,
                            'text-success': tutor.last_debt.debt_type == 1,
                        }">
                            @{{ tutor.last_debt.debt }}
                        </span>
                    </td>
                    <td>
                        @{{ tutor.debt }}
                    </td>
                    <td width='300'>
                        <span ng-click="startComment(tutor)" class="glyphicon glyphicon-pencil opacity-pointer" ng-hide="tutor.list_comment || tutor.is_being_commented"></span>
                        <input type="text" class='no-border-outline tutor-list-comment' id='list-comment-@{{ tutor.id }}' maxlength="64" placeholder="введите комментарий..."
                            ng-model='tutor.list_comment'
                            ng-show='tutor.list_comment || tutor.is_being_commented'
                            ng-blur='blurComment(tutor)'
                            ng-focus='focusComment(tutor)'
                            ng-keyup='saveComment($event, tutor)'
                        >
                    </td>
                </tr>
            </tbody>
        </table>
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
    </div>
    <div class="row" ng-hide="tutors.length">
        <div class="col-sm-12">
            <h3 style="text-align: center; margin: 50px 0">cписок  пуст</h3>
        </div>
    </div>
</div>
@stop
