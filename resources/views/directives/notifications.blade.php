<div class="comment-block">
    <div ng-show='notifications.length > show_max && !show_all_notifications'>
        <span class='comment-add pointer' ng-click='showAllNotifications()'>все комментарии (@{{ notifications.length - show_max + 1 }})</span>
    </div>
    <div>
		<div ng-repeat="notification in getNotifications() | orderBy:'created_at'" id='notification-@{{ notification.id }}' data-notification-id='@{{ notification.id }}'>
			<div class='comment-div'>
				<span style="color: @{{notification.user.color}}" class="comment-login">@{{notification.user.login}} <span class='comment-time'>@{{ formatDateTime(notification.created_at) }}:</span></span>
				<div class='comment-line' ng-click="edit(notification, $event)">@{{notification.comment}}</div>
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
			<div style='border: 1px solid black' type="text" placeholder="введите комментарий..."
                ng-blur='start_notificationing = false'
                ng-model='comment'
                class='comment-line'
                contenteditable="true"
                ng-keydown='submitNotification($event)'>@{{ comment }}</div>
		</span>
	</div>
</div>
