<div class="row" ng-show="mode == 'map'">
  <div class="col-sm-12" style="overflow: hidden">
    <div class="map-loading" ng-show='loading'>
        <img src="svg/loaders/tail-spin.svg">
    </div>
    <map zoom="10" disable-default-u-i="true"
        scale-control="true"
        zoom-control="true"
        map-type-control="true"
        style="height: 500px; width: 100%"
    >
        <transit-layer></transit-layer>
        <custom-control position="TOP_RIGHT" index="1">
        </custom-control>
    </map>

    <div class="map-tutor-list">
        <div ng-repeat='tutor in tutor_list' class='temporary-tutor' ng-mousedown='startDragging(tutor)'>
            @include('tutors.add-to-list._tutor', ['tutor' => 'tutor'])
        </div>
        <div ng-if='hovered_tutor'>
            @include('tutors.add-to-list._tutor', ['tutor' => 'hovered_tutor'])
        </div>
    </div>

  </div>
</div>
