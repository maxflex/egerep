<div class="row mb">
    <div class="col-sm-4">
        <div class="form-group">
            <div class="form-gorup">
                <input type="text" class="form-control" placeholder="название" ng-model="FormService.model.name">
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <div class="form-gorup">
                <input type="text" class="form-control" placeholder="входящий остаток" ng-model="FormService.model.remainder">
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <div class="form-gorup">
                <input type="text" readonly placeholder="дата остатка" ng-model="FormService.model.remainder_date"
                  class="form-control bs-date-clear pointer">
            </div>
        </div>
    </div>
</div>
