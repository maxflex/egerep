<div class="small" ng-repeat='marker in markers' style="display: @{{ inline ? 'inline-block' : 'block' }}; position: relative" ng-init="stations = one_station ? [marker.metros[0]] : marker.metros">
    <span class="metro-circle" style="background: @{{ stations[0].station.color }}"></span>
    @{{ stations[0].station.title }}, @{{ marker.comment || 'адрес отсутствует' }}
    <span class="link-like link-reverse" style='margin-left: 10px' ng-click="editMarkerModal(marker)">редактировать</span>
    {{-- <div class="form-group" style='margin-top: 5px'>
        <input maxlength="128" ng-change="$parent.$parent.form_changed = true" class="form-control" type="text" placeholder="комментарий к метке" ng-model="marker.comment" />
    </div> --}}
</div>

{{-- РЕДАКТИРОВАНИЕ КОММЕНТАРИЯ --}}
<div id="marker-modal" class="modal" role="dialog" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Редактирование адреса</h4>
        </div>
      <div class="modal-body">
          <input maxlength="128" class="form-control" type="text" placeholder="адрес метки" ng-model="marker_comment" />
      </div>
      <div class="modal-footer center">
        <button type="button" class="btn btn-primary" ng-click="editMarker()">редактировать</button>
      </div>
    </div>
  </div>
</div>
