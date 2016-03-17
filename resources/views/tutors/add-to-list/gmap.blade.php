<div class="row">
  <div class="col-sm-12" style="overflow: hidden">
    <map zoom="10" disable-default-u-i="true" scale-control="true"
        zoom-control="true" zoom-control-options="{style:'SMALL'}" style="height: 500px; width: 100%">
        <transit-layer></transit-layer>
        <custom-control position="TOP_RIGHT" index="1">
        </custom-control>
    </map>

    <div class="map-tutor-list" ng-show='tutor_list.length > 0 || hovered_tutor'>
        <div ng-repeat='tutor in tutor_list'>
            @include('tutors.add-to-list._tutor', ['tutor' => 'tutor'])
        </div>
        <div ng-if='hovered_tutor'>
            @include('tutors.add-to-list._tutor', ['tutor' => 'hovered_tutor'])
        </div>
    </div>

  </div>
</div>
