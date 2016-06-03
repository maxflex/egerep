<td ng-repeat='client in clients' width='77' class="client-draggable" data-id='@{{ client.id }}'>
    <a href='@{{ client.link }}'>@{{ client.name | cut:false:10:'имя не указано' }}</a>
    <br>
    <span class='text-gray'>
        <span ng-show='client.grade'>@{{ Grades[client.grade] }}</span>
        <span ng-hide='client.grade'>класс не указан</span>
    </span>
    <div ng-click='accountInfo(client)' class='attachment-status @{{ client.state }}'></div>
</td>
