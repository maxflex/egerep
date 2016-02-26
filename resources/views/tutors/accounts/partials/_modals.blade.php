{{-- ДОБАВЛЕНИЕ РАСЧЕТА --}}
<div class="modal" id='add-account' tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Добавление расчета</h4>
            </div>
            <div class="modal-body">
                <input type="text" id='date-end' class="form-control" placeholder="дата конца периода" ng-model='new_account_date_end'>
            </div>
            <div class="modal-footer center">
                <button type="button" class="btn btn-primary" ng-click="addAccount()">Добавить</button>
            </div>
        </div>
    </div>
</div>


{{-- ИЗМЕНЕНИЕ ДАТЫ ВСТРЕЧИ --}}
<div class="modal" id='change-account-date' tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Изменить дату встречи</h4>
            </div>
            <div class="modal-body">
                <input type="text" id='date-end-change' class="form-control" placeholder="дата конца периода" ng-model='change_date_end'>
            </div>
            <div class="modal-footer center">
                <button type="button" class="btn btn-primary" ng-click="changeDate()">Изменить</button>
            </div>
        </div>
    </div>
</div>
