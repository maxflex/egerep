<div class="comment-block">
    <div ng-show='comments.length > show_max && !show_all_comments'>
        <span class='comment-add pointer' ng-click='showAllComments()'>все комментарии (@{{ comments.length - show_max + 1 }})</span>
    </div>
    <div>
		<div ng-repeat="comment in getComments() | orderBy:'created_at'" id='comment-@{{ comment.id }}' data-comment-id='@{{ comment.id }}'>
			<div class='comment-div'>
				<span class="comment-login">@{{ UserService.getLogin(comment.user_id) }} <span class='comment-time'>@{{ formatDateTime(comment.created_at) }}:</span></span>
				<div class='comment-line' ng-click="edit(comment, $event)">@{{ comment.comment }}</div>
                <span class="opacity-pointer text-danger" style='margin-left: 5px'
                    ng-click="remove(comment.id)" ng-show='comment.is_being_edited'>удалить</span>
			</div>
		</div>
	</div>
    <div style="height: 25px">
		<span ng-hide='start_commenting' class="pointer no-margin-right comment-add"
            ng-click='startCommenting($event)'>комментировать</span>
		<span class="comment-add-hidden" ng-show='start_commenting'>
			<span class="comment-add-login comment-login">
			@{{ user.nickname }}: </span>
			<input class="comment-add-field" type="text" placeholder="введите комментарий..."
                ng-blur='start_commenting = false'
                ng-model='comment'
                ng-keydown='submitComment($event)'>
		</span>
	</div>
</div>
