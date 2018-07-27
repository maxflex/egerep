<div style="margin-top: 20px; ">
    <span ng-show="!explanation_tutors.length && !explaination_tutors_loading" ng-click="getExplanationByTutors()" class="link-like">показать расшифровку по преподавателям</span>
    <span ng-show="explaination_tutors_loading" class="link-like">загрузка данных...</span>
    <table ng-show="!explaination_tutors_loading && explanation_tutors.length" class="table" style="font-size: 0.8em;">
        <thead class="bold">
        <tr>
            <td align="left">Преподаватель</td>
            <td>Сумма</td>
        </tr>
        </thead>
        <tbody>
            <tr ng-repeat="d in explanation_tutors">
                <td width="300">
                    <a href="/tutors/@{{ d.tutor_id }}/edit">@{{ d.last_name }} @{{ d.first_name }} @{{ d.middle_name }}</a>
                </td>
                <td>
                    @{{ d.sum | number }}
                </td>
            </tr>
        </tbody>
    </table>
</div>
