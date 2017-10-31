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
                <select class='form-control selectpicker' ng-model='FormService.model.group_id' ng-change='changeGroup()' convert-to-number-new>
                    <option value=''>не выбрано</option>
                    <option value='-1'>новая группа</option>
                    <option disabled>──────────────</option>
                    {{-- <option ng-repeat="(index, a) in arr">@{{ a }}</option> --}}
                    <option ng-repeat="g in groups track by $index" value="@{{ g.id }}">
                        @{{ g.name }}
                    </option>
                </select>
            </div>
        </div>
    </div>
</div>
@include('payments.expenditures._modals')