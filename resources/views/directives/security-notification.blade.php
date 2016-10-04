<div class="">
    <span ng-repeat="selected in tutor.security_notification track by $index" class="circle-item" ng-class="{
        'selected': selected
    }" ng-click='toggleNotification($index)'></span>
</div>
