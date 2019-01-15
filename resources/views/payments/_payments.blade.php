<div ng-show="tab == 'payments'">
    <div class="row flex-list">
        <div>
            <label>источник</label>
            <select multiple title="не выбрано" ng-model="search.source_ids" class="selectpicker" ng-change="filter()">
                <option ng-repeat="source in sources" value="@{{ source.id }}">@{{ source.name }}</option>
            </select>
        </div>
        <div>
            <label>адресат</label>
            <select multiple title="не выбрано" ng-model="search.addressee_ids" class="selectpicker" ng-change="filter()">
                <option ng-repeat="source in sources" value="@{{ source.id }}">@{{ source.name }}</option>
            </select>
        </div>
        <div>
            <label>статья</label>
            <select multiple title="не выбрано" ng-model="search.expenditure_ids" class="selectpicker expenditure-select" ng-change="filter()">
                <optgroup ng-repeat="expenditure in expenditures" label="@{{ expenditure.name }}">
                    <option ng-repeat="d in expenditure.data" value="@{{ d.id }}">@{{ d.name }}</option>
                </optgroup>
            </select>
        </div>
        <div>
            <label>назначение</label>
            <input type="text" id='payment-purpose' ng-keyup='keyFilter($event)' class="form-control" ng-model="search.purpose" placeholder="не выбрано">
        </div>
    </div>

    <table class="table reverse-borders" style='font-size: 13px'>
        <tr ng-repeat="model in IndexService.page.data">
            <td width='10'>
                {{-- <img src="img/svg/copy-file.svg" class="pointer" style='width: 13px; margin-right: 5px; outline: none' ng-click="clonePayment(model)" /> --}}
                {{-- <i class="fa fa-pencil pointer text-success" aria-hidden="true" ng-click="editPayment(model)"></i> --}}
                <i class="fa fa-pencil pointer text-success" aria-hidden="true" ng-click="setPaymentActionsIndex($index)"></i>
                <div class="custom-dropdown" ng-show="payment_actions_index === $index" style='width: 150px'>
                    <div class="custom-dropdown__item" ng-click='editPayment(model)'>
                        редактировать
                    </div>
                    <div class="custom-dropdown__item" ng-click='clonePayment(model)'>
                        клонировать
                    </div>
                </div>
            </td>
            <td>
                @{{ model.sum | number }}
            </td>
            <td>
                @{{ model.date }}
            </td>
            <td>
                @{{ findById(sources, model.source_id).name }}
            </td>
            <td>
                @{{ findById(sources, model.addressee_id).name }}
            </td>
            <td>
                @{{ getExpenditure(model.expenditure_id).name }}
            </td>
            {{-- <td>
                @{{ UserService.getLogin(model.user_id) }}: @{{ formatDateTime(model.created_at) }}
            </td> --}}
            <td width="250">
                @{{ model.purpose | cut:false:20 }}
            </td>
        </tr>
    </table>
    @include('modules.pagination-new')
</div>
