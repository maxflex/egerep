<div ng-show="mode == 'list'" class="row">
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
                            'text-danger': tutor.last_account_info.debt_type == 0,
                            'text-success': tutor.last_account_info.debt_type == 1,
                        }">
                            @{{ tutor.last_account_info.debt }}
                        </span>
                    </td>
                    <td>
                        @{{ tutor.debt }}
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
            </tbody>
        </table>
    </div>
</div>
