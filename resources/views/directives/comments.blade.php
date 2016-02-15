{{-- @{{ entity.phone }} | @{{ level }} --}}
<div class="comment-block">
    <div>
		<div ng-repeat="comment in comments">
			<div id="comment-block-@{{comment.id}}">
				<span style="color: @{{comment.user.color}}" class="comment-login">@{{comment.user.login}}: </span>
				<div style="display: initial" id="comment-@{{comment.id}}" commentid="@{{comment.id}}" onclick="editComment(this)">@{{comment.comment}}</div>
				<span class="save-coordinates">@{{ $parent.formatDateTime(comment.created_at) }}</span>
				<span ng-attr-data-id="@{{comment.id}}"
					class="glyphicon opacity-pointer text-danger glyphicon-remove glyphicon-2px" onclick="deleteComment(this)"></span>
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
