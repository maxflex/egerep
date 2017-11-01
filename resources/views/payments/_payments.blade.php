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
            <label>тип</label>
            <ng-select-new model='search.type' object="PaymentTypes" label="title" none-text='тип'></ng-select-new>
        </div>
        <div>
            <label>проверено</label>
            <select ng-model="search.checked" class="selectpicker" ng-change="filter()">
                <option value="">не выбрано</option>
                <option disabled>──────────────</option>
                <option ng-repeat="c in Checked" value="@{{ $index }}">@{{ c }}</option>
            </select>
        </div>
        <div>
            <label>назначение</label>
            <input type="text" ng-keyup='keyFilter($event)' class="form-control" ng-model="search.purpose" placeholder="не выбрано">
        </div>
    </div>

    <table class="table reverse-borders" style='font-size: 13px'>
        <tr ng-repeat="model in IndexService.page.data" ng-class="{'selected': selected_payments.indexOf(model.id) !== -1}">
            <td width='110'>
                <i class="fa fa-star table-star" ng-class="{'active': model.checked}" aria-hidden="true" ng-click="toggleEnumServer(model, 'checked', Checked, Payment)"></i>
                <img src="img/svg/copy-file.svg" class="pointer" style='width: 13px; margin-right: 5px; outline: none' ng-click="clonePayment(model)" />
                <a class="pointer" ng-click="editPayment(model)">@{{ findById(PaymentTypes, model.type).title }}</a>
            </td>
            <td ng-click="selectPayment(model)">
                @{{ model.sum | number }}
            </td>
            <td ng-click="selectPayment(model)">
                @{{ model.date }}
            </td>
            <td ng-click="selectPayment(model)">
                @{{ findById(sources, model.source_id).name }}
            </td>
            <td ng-click="selectPayment(model)">
                @{{ findById(sources, model.addressee_id).name }}
            </td>
            <td ng-click="selectPayment(model)">
                @{{ findById(expenditures, model.expenditure_id).name }}
            </td>
            {{-- <td>
                @{{ UserService.getLogin(model.user_id) }}: @{{ formatDateTime(model.created_at) }}
            </td> --}}
            <td width="250" ng-click="selectPayment(payment)">
                @{{ model.purpose | cut:false:20 }}
            </td>
        </tr>
    </table>
    @include('modules.pagination-new')
</div>
