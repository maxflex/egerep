<div class="row mb">
    <div class="col-sm-4">
        <div class="form-group">
            <ng-select-new model='FormService.model.type' object="PaymentTypes" label="title" convert-to-number></ng-select-new>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <div class="input-group custom">
              <span class="input-group-addon">сумма – </span>
              <input class="form-control digits-only-float" ng-model="FormService.model.sum">
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <div class="form-group">
                <div class="input-group custom">
                  <span class="input-group-addon">дата –</span>
                  <input type="text" class="form-control bs-date-top"
                      ng-model="FormService.model.date">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mb">
    <div class="col-sm-4">
        <div class="form-group">
            <ng-select-new model='FormService.model.source_id' object="sources" label="name" convert-to-number none-text='источник'></ng-select-new>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <ng-select-new model='FormService.model.addressee_id' object="sources" label="name" convert-to-number none-text='адресат'></ng-select-new>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <ng-select-new model='FormService.model.expenditure_id' object="expenditures" label="name" convert-to-number none-text='статья'></ng-select-new>
        </div>
    </div>
</div>
<div class="row mb">
    <div class="col-sm-12">
        <textarea class="form-control" ng-model="FormService.model.purpose" rows="5" placeholder="назначение"></textarea>
    </div>
</div>
