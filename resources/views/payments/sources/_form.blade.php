<div class="row mb">
    <div class="col-sm-4">
        <div class="form-group">
            <div class="form-gorup">
                <input type="text" class="form-control" placeholder="название" ng-model="FormService.model.name">
            </div>
        </div>
    </div>
</div>
<div class="row mb">
    <div class="col-sm-12">
        <table class="table reverse-borders">
            <tr ng-repeat="r in FormService.model.remainders">
                <td>
                    @{{ r.date }}
                </td>
                <td>
                    @{{ r.remainder | number }}
                </td>
                <td style='text-align: right'>
                    <span class="link-like" style='margin-right: 10px' ng-click="editRemainder(r)">редактировать</span>
                    <span class="link-like text-danger" ng-click="deleteRemainder(r)">удалить</span>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <span class="link-like" ng-click="addRemainderDialog()">добавить</span>
                </td>
            </tr>
        </table>
    </div>
</div>
@include('payments.sources/_modals')