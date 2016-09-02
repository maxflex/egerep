{{-- ВЫБОР ВРЕМЕНИ --}}
<div id="choose-time" class="modal" role="dialog" tabindex="-1">
  <div class="modal-dialog" style='width: 300px'>
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title center">Редактировать время</h4>
      </div>
      <div class="modal-body">
          <div style='display: flex; justify-content: center; align-items: center'>
              <div class='inline-block' uib-timepicker ng-model="d.startsAt" hour-step="1" minute-step="1" show-meridian="ismeridian"></div>
              <span style='margin: 0 10px'>–</span>
              <div class='inline-block' uib-timepicker ng-model="d.endsAt" hour-step="1" minute-step="1" show-meridian="ismeridian"></div>
          </div>
      </div>
      <div class="modal-footer center">
        <button type="button" class="btn btn-primary" ng-click="editTime()">Редактировать</button>
      </div>
    </div>
  </div>
</div>
