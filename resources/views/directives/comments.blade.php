<div class="comment-block">
    <div>
		<div ng-repeat="comment in comments | orderBy:'created_at'">
			<div class='comment-div'>
				<span style="color: @{{comment.user.color}}" class="comment-login">@{{comment.user.login}}: </span>
				<div class='comment-line' ng-click="edit(comment, $event)">@{{comment.comment}}</div>
				<span class="save-coordinates">@{{ formatDateTime(comment.created_at) }}</span>
				<span class="glyphicon opacity-pointer text-danger glyphicon-remove glyphicon-2px" ng-click="remove(comment)"></span>
			</div>
		</div>
	</div>
    <div style="height: 25px">
		<span ng-hide='start_commenting' class="pointer no-margin-right comment-add"
            ng-click='startCommenting($event)'>комментировать</span>
		<span class="comment-add-hidden" ng-show='start_commenting'>
			<span class="comment-add-login comment-login" style="color: @{{ user.color }}">
			@{{ user.login }}: </span>
			<input class="comment-add-field" type="text" placeholder="введите комментарий..."
                ng-blur='start_commenting = false'
                ng-model='comment'
                ng-keydown='submitComment($event)'>
		</span>
	</div>
</div>
