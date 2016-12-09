<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <b>отзыв</b>
            <span ng-hide='selected_attachment.review' class="link-like link-gray" style="margin-left: 10px"
                ng-click="toggleReview()">написать отзыв</span>
            @if($user->allowed(\Shared\Rights::ER_DELETE_REVIEWS))
            <span ng-show='selected_attachment.review' class="link-like link-gray" style="margin-left: 10px"
                ng-click="toggleReview()">удалить отзыв</span>
            @endif
        </div>
    </div>
</div>

<div class="row mb" ng-if='selected_attachment.review'>
    <div class="col-sm-3">
        <div class="form-group">
            <select class="form-control" ng-model='selected_attachment.review.score'
                ng-options='+(score_id) as label for (score_id, label) in ReviewScores'>
                <option value="">оценка репетитору</option>
            </select>
        </div>
        <div class="form-group"><input type="text" class="form-control" ng-model='selected_attachment.review.signature' placeholder="подпись"></div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            <textarea style="height: 75px" cols="40" class="form-control" ng-model='selected_attachment.review.comment'></textarea>
        </div>
    </div>
    <div class="col-sm-6">
        <div class='mbs'>
            <b>Отзыв создан:</b> @{{ selected_attachment.review.user_login }} @{{ formatDateTime(selected_attachment.review.created_at) }}
        </div>
        <div class='mbs'>
            <b>Статус:</b> <span class="link-like"
                ng-click="toggleEnum(selected_attachment.review, 'state', ReviewStates)">@{{ ReviewStates[selected_attachment.review.state] }}</span>
        </div>
    </div>

    <div class="col-sm-12">
        <comments entity-type='review' entity-id='selected_attachment.id' user='{{ $user }}'></comments>
    </div>
</div>
