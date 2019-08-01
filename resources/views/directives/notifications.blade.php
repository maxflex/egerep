<div class="comment-block">
    <div ng-show='notifications.length > show_max && !show_all_notifications'>
        <span class='comment-add pointer' ng-click='showAllNotifications()'>все напоминания (@{{ notifications.length - show_max + 1 }})</span>
    </div>
    <div>
		<div ng-repeat="notification in getNotifications() | orderBy:'created_at'" id='notification-@{{ notification.id }}' data-notification-id='@{{ notification.id }}'>
			<div class='comment-div'>
				<span class="comment-login">@{{ notification.user.nickname || 'system' }} <span class='comment-time'>@{{ formatDateTime(notification.created_at) }}:</span></span>
				<div class='new-notification' placeholder="текст напоминания" contenteditable="true"
                     ng-keydown="editNotification(notification, $event)"
                     ng-blur='unsetEditing(notification)'
                     ng-focus='setEditing(notification)'
                     ng-click='hack(notification, $event)'
                >@{{notification.comment}}</div>
                <span>–</span>
                <input class="notification-date-add" type="text" placeholder="дата"
                       ng-keydown='editNotification(notification, $event)'
                       ng-model='notification.date'
                       ng-click='setEditing(notification)'
                       ng-focus='setEditing(notification)'
                       ng-blur='unsetEditing(notification)'
                >
                <span
                    class='link-like-no-color'
                    ng-click="toggle(notification)"
                    ng-class="{
                    'text-danger': notification.approved == 0
                }">@{{ Notify[notification.approved] }}</span>
                <span class="opacity-pointer text-danger" style='margin-left: 5px'
                      ng-click="remove(notification.id)" ng-show='notification.is_being_edited'>удалить</span>
			</div>
		</div>
	</div>
    <div style="height: 25px">
        <span ng-hide='start_notificationing'>
            <span class="pointer no-margin-right comment-add" ng-click='startNotificationing($event)'>добавить напоминание</span>
            <span ng-hide='notifications.length'>
                <span class='text-gray'>или</span>
                <span class="pointer no-margin-right comment-add" ng-click='defaultNotification()'>стандартное напоминание</span>
            </span>
        </span>
		<span class="comment-add-hidden" ng-show='start_notificationing'>
			<span class="comment-add-login notification-login">
			@{{ user.nickname }}: </span>
			<div placeholder="текст напоминания"
                class='new-notification'
                contenteditable="true"
                ng-keydown='submitNotification($event)'
            ></div>
            <span class='text-gray'>–</span>
            <input class="notification-date-add" type="text" placeholder="дата" ng-keydown='submitNotification($event)'>
            @{{ date }}
		</span>
	</div>
</div>
