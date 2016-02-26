<div ng-repeat='marker in markers'>
    <span ng-repeat='metro in marker.metros'>
        <span class="label label-metro-short" style="background: @{{ metro.station.color }}; margin-right: 3px">@{{ short(metro.station.title) }} @{{ minutes(metro.minutes) }}@{{ metro.distance > 1000 ? 'т' : 'п' }}</span>
    </span>
     –
    <span ng-if="marker.type == 'green'">тут занятия возможны</span>
    <span ng-if="marker.type == 'red'">тут занятия не возможны</span>
    <span ng-if="marker.type == 'blue'">голубая метка</span>
</div>
