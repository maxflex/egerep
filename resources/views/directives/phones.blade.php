@foreach(['', 2, 3, 4] as $phone_id)
<div class="form-group" ng-show="level >= {{ $phone_id ? $phone_id : 1 }}">
    <div ng-class="{'input-group': isFull(entity.phone{{ $phone_id }})
        || ({{ $phone_id ? $phone_id : 1 }} == level && level < 3)}">
        <input ng-keyup="phoneMaskControl($event)" type="text"
        class="form-control phone-masked" ng-model="entity.phone{{ $phone_id }}" placeholder="телефон {{ $phone_id }}">
        <div class="phone_comment_holder">
           <span class="glyphicon glyphicon-pencil opacity-pointer"
                 ng-click='startPhoneComment("phone{{ $phone_id ? $phone_id : '' }}_comment")'
                 ng-hide='entity.phone{{ $phone_id ? $phone_id : '' }}_comment || entity.is_being_commented["phone{{ $phone_id ? $phone_id : '' }}_comment"]'></span>

           <input type="text" id="phone{{ $phone_id ? $phone_id : '' }}_comment" class="no-border-outline" maxlength="64" placeholder="введите комментарий..."
                 ng-model='entity.phone{{ $phone_id ? $phone_id : '' }}_comment'
                 ng-show='entity.phone{{ $phone_id ? $phone_id : '' }}_comment || entity.is_being_commented["phone{{ $phone_id ? $phone_id : '' }}_comment"]'
                 ng-blur='blurPhoneComment("phone{{ $phone_id ? $phone_id : '' }}_comment")'
                 ng-focus='focusPhoneComment("phone{{ $phone_id ? $phone_id : '' }}_comment")'
                 ng-keyup='savePhoneComment($event, "phone{{ $phone_id ? $phone_id : '' }}_comment")'>
        </div>
        <div class="input-group-btn">
            <button class="btn btn-default" ng-if="isFull(entity.phone{{ $phone_id }})" ng-click='PhoneService.call(entity.phone{{ $phone_id }})'>
                <span class="glyphicon glyphicon-earphone small no-margin-right"></span>
            </button>
            <button class="btn btn-default"
                ng-click='sms(entity.phone{{ $phone_id }})'
                ng-if="isFull(entity.phone{{ $phone_id }}) && isMobile(entity.phone{{ $phone_id }})">
                <span class="glyphicon glyphicon-envelope small no-margin-right"></span>
            </button>
            <button class="btn btn-default" ng-if="level == {{ $phone_id ? $phone_id : 1 }} && level < 4" ng-click="nextLevel()">
                <span class="glyphicon glyphicon-plus small no-margin-right"></span>
            </button>
        </div>
    </div>
</div>
<style>
    /**
    * @todo move to styles
    */
    .email_comment_holder,.phone_comment_holder{
        position: absolute;
        left: 170px;
        right: 60px;
        z-index: 10;
    }
    .email_comment_holder span,.phone_comment_holder span{
        line-height: 34px;
    }
    .email_comment_holder input,.phone_comment_holder input{
        width: 142px;
        height: 30px;
        margin-top: 2px;
    }
</style>
@endforeach
