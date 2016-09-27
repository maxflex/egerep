<td ng-if='!clients.length' class="fake-cell"></td>
<td ng-repeat='client in clients' width='77' class="client-draggable" data-id='@{{ client.id }}'>
    <a href='@{{ client.link }}'>@{{ client.name | cut:false:10:'без имени' }}</a>
    <br>
    <span class='text-gray'>
        <span ng-show='client.grade'>@{{ Grades[client.grade] }}</span>
        <span ng-hide='client.grade'>без класса</span>
    </span>
    <div ng-click='accountInfo(client)' class='attachment-status @{{ client.state }}'></div>
</td>
