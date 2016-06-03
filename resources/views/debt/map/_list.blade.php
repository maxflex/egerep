<div ng-show="mode == 'list'" class="row">
    <div class="col-sm-12">
        <table class="table">
            <thead class="bold">
                <tr>
                    <td>ПРЕПОДАВАТЕЛЬ</td>
                    <td>
                        <span ng-click="sortType = 'last_debt'; sortReverse = !sortReverse">ДОЛГ</span>
                    </td>
                    <td>
                        <span ng-click="sortType = 'debt_calc'; sortReverse = !sortReverse">ДЕБЕТ</span>
                    </td>
                    <td>
                        <span ng-click="sortType = 'last_account_info.date_end'; sortReverse = !sortReverse">ПОСЛЕДНИЙ РАСЧЕТ</span>
                    </td>
                    <td>КОММЕНТАРИЙ</td>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="tutor in tutors | orderBy:sortType:sortReverse">
                    <td>
                        <a href='tutors/@{{ tutor.id }}/edit'>@{{ tutor.full_name }}</a>
                    </td>
                    <td>
                        <span ng-class="{
                            'text-danger': tutor.last_account_info.debt_type == 0,
                            'text-success': tutor.last_account_info.debt_type == 1,
                        }">
                            @{{ tutor.last_account_info.debt }}
                        </span>
                    </td>
                    <td>
                        @{{ tutor.debt_calc }}
                    </td>
                    <td>
                        @{{ formatDate(tutor.last_account_info.date_end) }}
                    </td>
                    <td width='300'>
                        <span ng-click="startComment(tutor)" class="glyphicon glyphicon-pencil opacity-pointer" ng-hide="tutor.debt_comment || tutor.is_being_commented"></span>
                        <input type="text" class='no-border-outline tutor-list-comment' id='list-comment-@{{ tutor.id }}' maxlength="64" placeholder="введите комментарий..."
                            ng-model='tutor.debt_comment'
                            ng-show='tutor.debt_comment || tutor.is_being_commented'
                            ng-blur='blurComment(tutor)'
                            ng-focus='focusComment(tutor)'
                            ng-keyup='saveComment($event, tutor)'
                        >
                    </td>
                </tr>
                <tfoot ng-show='tutors && tutors.length'>
                    <tr>
                        <td></td>
                        <td>
                            <span ng-class="{
                                'text-danger': totalLastDebt().debt_type == 0,
                                'text-success': totalLastDebt().debt_type == 1,
                            }">
                                @{{ totalLastDebt().debt }}
                            </span>
                        </td>
                        <td>@{{ total(tutors, 'debt') }}</td>
                        <td>@{{ total(tutors, 'debt_calc') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </tbody>
        </table>
    </div>
</div>
