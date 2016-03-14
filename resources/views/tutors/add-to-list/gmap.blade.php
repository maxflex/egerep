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
            <tutor-photo tutor='tutor' class="ava"></tutor-photo>
            <div class="info-line">
                <a href="tutors/@{{ tutor.id }}/edit" target="_blank">@{{ tutor.full_name }}</a>
                <span ng-repeat='phone in tutor.phones' title="@{{phone}}" ng-click='PhoneService.call(phone)'
                    class="opacity-pointer glyphicon glyphicon-earphone pull-right small"></span>
            </div>
            <div class="info-line">
                <plural count='tutor.age' type='age'></plural>
                <span class='remove-space' ng-show='tutor.public_price > 0'>, @{{ tutor.public_price }} р.</span>
                <span ng-show='tutor.departure_price > 0'>+ выезд от @{{ tutor.departure_price }} р.</span>
            </div>
            <div class="info-line">
                <plural count='tutor.clients_count' type='student' none-text='учеников нет'></plural>, <plural count='tutor.meeting_count' type='meeting' none-text='встреч нет'></plural>
            </div>
        </div>
        <div ng-if='hovered_tutor'>
            {{-- <tutor-photo tutor='hovered_tutor' class="ava"></tutor-photo> --}}
            <img class='ava' src="img/tutors/@{{ hovered_tutor.has_photo_cropped ? hovered_tutor.id + '@2x.' + hovered_tutor.photo_extension : 'no-profile-img.gif' }}">
            <div class="info-line">
                <a href="tutors/@{{ hovered_tutor.id }}/edit" target="_blank">@{{ hovered_tutor.full_name }}</a>
                <span ng-repeat='phone in hovered_tutor.phones' title="@{{phone}}" ng-click='PhoneService.call(phone)'
                    class="opacity-pointer glyphicon glyphicon-earphone pull-right small"></span>
            </div>
        </div>
    </div>

  </div>
</div>
