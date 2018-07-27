<div style="margin-top: 20px; ">
    <span ng-show="!stats.efficency.length && !explaination_loading" ng-click="getExplanation()" class="link-like">показать расшифровку по стыковкам</span>
    <span ng-show="explaination_loading" class="link-like">загрузка данных...</span>
    <table ng-show="!explaination_loading && stats.efficency.length" class="table" style="font-size: 0.8em;">
        <thead class="bold">
        <tr>
            <td align="left">Cтыковка</td>
            <td>Преподаватель</td>
            <td>Cтыковка</td>
            <td>Количество занятий</td>
            <td>Прогноз</td>
            <td>Статус</td>
            <td>Реквизиты</td>
            <td>Заявка</td>
            <td>Эффективность</td>
            <td>Доля заявки</td>
        </tr>
        </thead>
        <tbody>
            <tr ng-repeat-start="request in stats.efficency" ng-if="!request.attachments.length">
                <td align="left" colspan="6" width="44%"></td>
                <td width='20%'>
                    @{{ UserService.getLogin(request.user_id) }} @{{ formatDateTime(request.created_at) }}
                </td>
                <td><a href="requests/@{{ request.id }}/edit">@{{ request.id }}</a></td>
                <td>0</td>
                <td>@{{ isDenied(request) ? 1 : 0 }}</td>
            </tr>
            <tr ng-repeat-end ng-repeat="attachment in request.attachments">
                <td align="left" width="5%">
                    <a href="requests/@{{ request.id }}/edit#@{{ attachment.request_list_id }}#@{{ attachment.id }}">@{{ attachment.id }}</a>
                </td>
                <td align="left" width="23%">
                    <a href="tutors/@{{ attachment.tutor_id }}/edit">@{{ attachment.tutor.full_name}}</a>
                </td>
                <td width="6%">
                    @{{ attachment.date }}
                </td>
                <td>
                    @{{ attachment.account_data_count | hideZero }}<plus previous='attachment.account_data_count' count='attachment.archive.total_lessons_missing'></plus>
                </td>
                <td>
                    @{{ attachment.forecast }}
                </td>
                <td width='10%'>
                    @{{ AttachmentService.getStatus(attachment) }}
                </td>

                <td width='20%'>
                    @{{ UserService.getLogin(attachment.user_id) }}: @{{ formatDateTime(attachment.created_at) }}
                </td>
                <td><a href="requests/@{{ request.id }}/edit">@{{ request.id }}</a></td>
                <td>@{{ attachment.rate }}</td>
                <td>@{{ attachment.share }}</td>
            </tr>
            <tr>
                <td align="left" colspan="8"></td>
                <td>@{{ sumEfficency() | number }}</td>
                <td>@{{ sumShare() | number }}</td>
            </tr>
        </tbody>
    </table>
</div>
