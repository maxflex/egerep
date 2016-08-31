<div class="comment-block">
    <div ng-show='notifications.length > show_max && !show_all_notifications'>
        <span class='comment-add pointer' ng-click='showAllNotifications()'>все напоминания (@{{ notifications.length - show_max + 1 }})</span>
    </div>
    <div>
		<div ng-repeat="notification in getNotifications() | orderBy:'created_at'" id='notification-@{{ notification.id }}' data-notification-id='@{{ notification.id }}'>
			<div class='comment-div'>
				<span style="color: @{{notification.user.color}}" class="comment-login">@{{ notification.user.login || 'system' }} <span class='comment-time'>@{{ formatDateTime(notification.created_at) }}:</span></span>
				<div class='new-notification' placeholder="текст напоминания" contenteditable="true" ng-keydown="editNotification(notification, $event)" ng-click='hack($event)'>@{{notification.comment}}</div>
                <span>–</span>
                <input class="notification-date-add" type="text" placeholder="дата" ng-keydown='editNotification(notification, $event)' ng-model='notification.date'>
                <span
                    class='link-like-no-color'
                    ng-click="toggle(notification)"
                    ng-class="{
                    'text-danger': notification.approved == 0,
                    'text-success': notification.approved != 0
                }">@{{ Approved[notification.approved] }}</span>
			</div>
		</div>
	</div>
    <div style="height: 25px">
		<span ng-hide='start_notificationing' class="pointer no-margin-right comment-add"
            ng-click='startNotificationing($event)'>добавить напоминание</span>
        <div class="drop-delete-pad" id='notification-delete-@{{ entityType }}-@{{ entityId }}' ng-show='is_dragging' style="margin-left: 8px">удалить</div>
		<span class="comment-add-hidden" ng-show='start_notificationing'>
			<span class="comment-add-login notification-login" style="color: @{{ user.color }}">
			@{{ user.login }}: </span>
			<div placeholder="текст напоминания"
                ng-model='comment'
                class='new-notification'
                contenteditable="true"
                ng-keydown='submitNotification($event)'></div>
            <span class='text-gray'>–</span>
            <input class="notification-date-add" type="text" placeholder="дата" ng-keydown='submitNotification($event)'>
            @{{ date }}
		</span>
	</div>
</div>
