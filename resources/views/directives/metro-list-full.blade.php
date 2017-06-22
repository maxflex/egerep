<span ng-init='transport_distance = {{ App\Models\Metro::TRANSPORT_DISTANCE }}'></span>
<div ng-repeat='marker in markers' style="display: inline-block; margin-right: 3px" ng-init="metro = marker.metros[0]">
    <span class="metrocircle" style="background: @{{ metro.station.color }}"></span>@{{ metro.station.title }}
    <plural type='minute' count='minutes(metro.minutes)'></plural>
    @{{ metro.meters > transport_distance ? 'транспортом' : 'пешком' }}@{{ $last ? ', ' : ', ' }}
</div>
