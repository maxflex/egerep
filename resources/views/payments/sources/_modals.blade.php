{{-- ДОБАВЛЕНИЕ ПЛАТЕЖА --}}
<div id="remainder-stream-modal" class="modal" role="dialog" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
          <div class="form-group simpleinput-wrapper">
              <label>дата</label>
              <input type="text" class="bs-date-top datemask" ng-model="modal_remainder.date">
              <i class="fa fa-caret-down" aria-hidden="true"></i>
          </div>
          <div class="form-group simpleinput-wrapper">
              <label>сумма</label>
              <input type="text" ng-model="modal_remainder.remainder_comma" class="digits-only-floatcomma-minus">
          </div>
      </div>
      <div class="modal-footer center">
        <button type="button" class="btn btn-primary" ng-disabled="adding_remainder" ng-click="saveRemainder()">@{{ modal_remainder.id ? 'редактировать' : 'добавить' }}</button>
      </div>
    </div>
  </div>
</div>
