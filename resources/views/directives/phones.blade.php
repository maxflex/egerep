@foreach(App\Traits\Person::$phone_fields as $index => $phone_field)
    <div class="form-group" ng-class="{'has-error': entity.{{ $phone_field }}_duplicate}" ng-show="level >= {{ $index + 1 }}">
        <div ng-class="{'input-group': isFull(entity.{{ $phone_field }})
            || ({{ $index + 1 }} == level && level < {{ count(App\Traits\Person::$phone_fields) }})}">
            <input ng-keyup="phoneMaskControl($event)" type="text"
                class="form-control phone-masked" ng-model="entity.{{ $phone_field }}" placeholder="телефон {{ $index ? ($index + 1) : '' }}">

            <input-comment entity='entity' comment-field='{{ $phone_field }}_comment'></input-comment>

            <div class="input-group-btn">
                <button class="btn btn-default" ng-if="isFull(entity.{{ $phone_field }})" ng-click='info(entity.{{ $phone_field }})'>
                    <span class="glyphicon glyphicon-transfer small no-margin-right"></span>
                </button>
                <button class="btn btn-default" ng-if="isFull(entity.{{ $phone_field }})" ng-click='PhoneService.call(entity.{{ $phone_field }})'>
                    <span class="glyphicon glyphicon-earphone small no-margin-right"></span>
                </button>
                <button class="btn btn-default"
                    ng-click='sms(entity.{{ $phone_field }})'
                    ng-if="isFull(entity.{{ $phone_field }}) && PhoneService.isMobile(entity.{{ $phone_field }})">
                    <span class="glyphicon glyphicon-envelope small no-margin-right"></span>
                </button>
                <button class="btn btn-default" ng-if="level == {{ $index + 1 }} && level < {{ count(App\Traits\Person::$phone_fields) }}" ng-click="nextLevel()">
                    <span class="glyphicon glyphicon-plus small no-margin-right"></span>
                </button>
            </div>
        </div>
    </div>
@endforeach

{{-- API --}}
<div id="api-phone-info" class="modal" role="dialog" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
         <h1>info: @{{ api_number }}</h1>
      </div>
    </div>
  </div>
</div>
