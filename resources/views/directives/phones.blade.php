{{-- @{{ entity.phone }} | @{{ level }} --}}
@foreach(['', 2, 3] as $phone_id)
<div class="form-group" ng-show="level >= {{ $phone_id ? $phone_id : 1 }}">
    <div ng-class="{'input-group': isFull(entity.phone{{ $phone_id }})
        || ({{ $phone_id ? $phone_id : 1 }} == level && level < 3)}">
        <input ng-keyup="phoneMaskControl($event)" type="text"
        class="form-control phone-masked" ng-model="entity.phone{{ $phone_id }}" placeholder="телефон {{ $phone_id }}">
        <div class="input-group-btn">
            <button class="btn btn-default" ng-if="isFull(entity.phone{{ $phone_id }})">
                <span class="glyphicon glyphicon-earphone no-margin-right"></span>
            </button>
            <button class="btn btn-default" ng-if="isFull(entity.phone{{ $phone_id }}) && isMobile(entity.phone{{ $phone_id }})">
                <span class="glyphicon glyphicon-envelope no-margin-right"></span>
            </button>
            <button class="btn btn-default" ng-if="level == {{ $phone_id ? $phone_id : 1 }} && level < 3" ng-click="nextLevel()">
                <span class="glyphicon glyphicon-plus no-margin-right"></span>
            </button>
        </div>
    </div>
</div>
@endforeach
