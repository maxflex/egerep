<td ng-repeat='client in clients' width='100' ng-class="{'no-border-right': clients.length < 10}">
    <div class='mbs'>
        <a href='@{{ client.link }}'>@{{ client.name | cut:false:10:'без имени' }}</a>
    </div>
    <div class='mbs'>
        <span ng-show='client.grade'>@{{ Grades[client.grade] }}</span>
        <span ng-hide='client.grade'>без класса</span>
    </div>
    <div class='mbs'>
        @{{ AttachmentState[client.state] }}
    </div>
    <div class='space-row'></div>
    <div class='mbs'>
        с <span style='margin-top: 3px;'>@{{ formatDate(client.attachment_date) }}</span>
    </div>
    <div class='mbs'>
        <span ng-show='client.total_lessons'>@{{ client.total_lessons }}</span><span class='text-gray' ng-init='count = client.total_lessons_missing ? client.total_lessons_missing : client.total_lessons'><span ng-show='client.total_lessons > 0 && client.total_lessons_missing > 0'>+</span><span ng-show='client.total_lessons_missing'>@{{ client.total_lessons_missing }}</span></span> <plural count='count' type='lesson' text-only hide-zero></plural>&nbsp;
    </div>
    <div class='mbs'>
        <span ng-show='client.forecast'>
            <i class='fa fa-ruble filled' ng-class='{"half" : client.archive_date}'></i> @{{ client.forecast }}
        </span>&nbsp;
    </div>
    <div class='mbs'>
        <span ng-show='client.archive_date'>
            по <span class="text-danger">@{{ formatDate(client.archive_date) }}</span>
        </span>&nbsp;
    </div>
    <div class='double-space-row'></div>
    <div class='mbs'>
        <span class='link-like' ng-click='accountInfo(client)'>подробнее</span>
    </div>
</td>
{{--<td ng-if="clients.length < 10" width="100%" class="no-border-outline"></td>--}}