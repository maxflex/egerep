<div class="form-group">
    <div ng-class="{'input-group': isFull(phone)}">
        <input type="text" ng-keyup="phoneMaskControl($event)" class="form-control phone-masked" ng-model="phone" placeholder="отправить СМС">

        <div class="input-group-btn">
            <button class="btn btn-default"
                ng-click='ssms(phone)'
                ng-if="isFull(phone) && PhoneService.isMobile(phone)">
                <span class="glyphicon glyphicon-envelope small no-margin-right"></span>
            </button>
        </div>
    </div>
</div>
