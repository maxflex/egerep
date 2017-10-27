{{-- ДОБАВЛЕНИЕ ПЛАТЕЖА --}}
<div id="payment-stream-modal" class="modal" role="dialog" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
          <div class="form-group simpleinput-wrapper">
              <label>тип операции</label>
              <input readonly="true" type="text" value="@{{ findById(PaymentTypes, modal_payment.type).title }}">
              <i class="fa fa-caret-down" aria-hidden="true"></i>
              <select ng-model="modal_payment.type" class="hidden-select">
                <option ng-repeat="type in PaymentTypes" ng-value="type.id">@{{ type.title }}</option>
              </select>
          </div>
          <div class="form-group simpleinput-wrapper">
              <label>дата</label>
              <input type="text" class="bs-date-top datemask" ng-model="modal_payment.date">
              <i class="fa fa-caret-down" aria-hidden="true"></i>
          </div>
          <div class="form-group simpleinput-wrapper">
              <label>сумма</label>
              <input type="text" ng-model="modal_payment.sum">
          </div>
          <div class="form-group simpleinput-wrapper">
              <label>источник</label>
              <input readonly="true" type="text" value="@{{ modal_payment.source_id ? findById(sources, modal_payment.source_id).name : 'не указано' }}">
              <i class="fa fa-caret-down" aria-hidden="true"></i>
              <select ng-model="modal_payment.source_id" class="hidden-select">
                  <option value="">не указано</option>
                  <option disabled>──────────────</option>
                <option ng-repeat="source in sources" ng-value="source.id">@{{ source.name }}</option>
              </select>
          </div>
          <div class="form-group simpleinput-wrapper">
              <label>адресат</label>
              <input readonly="true" type="text" value="@{{ modal_payment.addressee_id ? findById(sources, modal_payment.addressee_id).name : 'не указано' }}">
              <i class="fa fa-caret-down" aria-hidden="true"></i>
              <select ng-model="modal_payment.addressee_id" class="hidden-select">
                  <option value="">не указано</option>
                  <option disabled>──────────────</option>
                <option ng-repeat="source in sources" ng-value="source.id">@{{ source.name }}</option>
              </select>
          </div>
          <div class="form-group simpleinput-wrapper">
              <label>статья</label>
              <input readonly="true" type="text" value="@{{ modal_payment.expenditure_id ? findById(expenditures, modal_payment.expenditure_id).name : 'не указано' }}">
              <i class="fa fa-caret-down" aria-hidden="true"></i>
              <select ng-model="modal_payment.expenditure_id" class="hidden-select">
                  <option value="">не указано</option>
                  <option disabled>──────────────</option>
                <option ng-repeat="expenditure in expenditures" ng-value="expenditure.id">@{{ expenditure.name }}</option>
              </select>
          </div>
          <div class="form-group simpleinput-wrapper">
              <label>назначение</label>
              <textarea ng-model="modal_payment.purpose" rows="3"></textarea>
          </div>
          <div>
              <input type="checkbox" name="checkbox" id="checkbox_id" ng-model="modal_payment.checked" ng-true-value="1" ng-false-value="0">
              <label for="checkbox_id" style='font-weight: normal'>проверено</label>
          </div>
          <div ng-hide="modal_payment.id">
              <input type="checkbox" name="checkbox" id="checkbox_id" ng-model="modal_payment.create_loan" ng-true-value="1" ng-false-value="0">
              <label for="checkbox_id" style='font-weight: normal'>создать заём</label>
          </div>
      </div>
      <div class="modal-footer center">
        <button ng-hide="modal_payment.id" type="button" class="btn btn-primary" ng-disabled="adding_payment" ng-click="savePayment()">добавить</button>
        <div ng-show="modal_payment.id" style='text-align: left'>
            <span class="text-gray">@{{ UserService.getLogin(modal_payment.user_id) }} @{{ formatDateTime(modal_payment.created_at) }}</span>
            <div class="pull-right">
                <span class="link-like" style='margin-right: 12px' ng-click="clonePayment()">копировать</span>
                <span class="link-like" style='margin-right: 12px' ng-click="savePayment()">сохранить</span>
                <span class="link-like text-danger" ng-click="deletePayment()">удалить</span>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
