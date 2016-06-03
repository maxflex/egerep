<div ng-show="mode == 'list'" class="row">
    <div class="col-sm-12">
        <table class="table">
            <thead class="bold">
                <tr>
                    <td>ПРЕПОДАВАТЕЛЬ</td>
                    <td>
                        <span class='link-like' ng-click="sortType = 'last_debt'; sortReverse = !sortReverse">ПОСЛЕДНЯЯ ЗАДОЛЖЕННОСТЬ</span>
                    </td>
                    <td>
                        <span class='link-like' ng-click="sortType = 'debt_calc'; sortReverse = !sortReverse">РАСЧЕТНЫЙ ДЕБЕТ</span>
                    </td>
                    <td>
                        <span class='link-like' ng-click="sortType = 'last_account_info.date_end'; sortReverse = !sortReverse">ДАТА ПОСЛЕДНОГО РАСЧЕТА</span>
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
                        @{{ tutor.last_account_info.debt_calc }}
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
                            @{{ total(tutors, 'last_account_info', 'debt_calc') }}
                        </td>
                        <td>@{{ total(tutors, 'debt_calc') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </tbody>
        </table>
    </div>
</div>
