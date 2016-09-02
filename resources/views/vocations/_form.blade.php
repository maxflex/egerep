<script src="//cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/2.0.0/ui-bootstrap-tpls.min.js"></script>
<div class="row">
    <div class="col-sm-4">
        <span class="opacity-pointer glyphicon glyphicon-menu-left cal-arrow"
            mwl-date-modifier
            date="calendarDate"
            decrement="calendarView">
        </span>
    </div>
    <div class="col-sm-4"><h1 class="center from-capital" style='display: block; margin-bottom: 10px'>@{{ getTitle() }}</h1></div>
    <div class="col-sm-4">
        <span class="opacity-pointer glyphicon glyphicon-menu-right cal-arrow pull-right"
            mwl-date-modifier
            date="calendarDate"
            increment="calendarView">
        </span>
    </div>
</div>


<div style='position: relative'>
    <mwl-calendar
        view="calendarView"
        view-date="calendarDate"
        events="vocation.data"
        view-title="calendarTitle"
        on-event-click='chooseTime(calendarEvent)'
        on-timespan-click="toggleVocation(calendarDate, calendarCell)"
        cell-is-open="true">
    </mwl-calendar>
    <div class='text-gray badge badge-vocation-days' ng-if='counter && vocation.data.length'>
        <plural count='vocation.data.length' type='day'></plural>
    </div>
</div>

<div ng-if='!index' class="row" style="margin-top: 15px; position: relative">
    <div class="blocker-div" ng-show='show'></div>
    <div class="col-sm-12">
        <textarea class="form-control" placeholder="комментарий к заявке" ng-model='vocation.comment'></textarea>
        <div class="input-group">
            <span style='position: relative; top: -3px'>Отработка: </span>
            <label class="ios7-switch transition-control" style="font-size: 24px; top: 1px">
                <input type="checkbox" ng-model="vocation.work_off" ng-true-value='1' ng-false-value='0'>
                <span class="switch"></span>
            </label>
        </div>
        <div ng-if='vocation.id' ng-repeat="user_id in [1, 56, 65]">
            <user model='UserService.get(user_id)'></user><span class='remove-space'>:</span> <span
                class='link-like-no-color'
                ng-click="approve(user_id)"
                ng-class="{
                'text-danger': !approved(user_id),
                'text-success': approved(user_id)
            }">@{{ approved(user_id) ? 'одобрено' : 'не одобрено' }}</span>
        </div>
    </div>
    {{-- <div class="col-sm-6">
    </div> --}}
</div>

@include('vocations._modals')
