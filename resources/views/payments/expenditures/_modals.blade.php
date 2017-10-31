<!-- Modal -->
<div id="new-group" class="modal" role="dialog" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header">
      <h4 class="modal-title">Создать группу</h4>
    </div>
      <div class="modal-body">
          <div class="row">
              <div class="col-sm-12">
                  <input class='form-control' ng-model='new_group_name' placeholder="новая группа">
              </div>
          </div>
      </div>
      <div class="modal-footer center">
        <button ng-disabled="!new_group_name" type="button" class="btn btn-primary" ng-click="createNewGroup()">создать группу</button>
      </div>
    </div>
  </div>
</div>