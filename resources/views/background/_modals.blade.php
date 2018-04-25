<div id="edit-background" class="modal" role="dialog" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Редактировать название</h4>
      </div>
      <div class="modal-body">
          <input type="text" ng-model="modal_background.title" placeholder="название" class="form-control" maxlength="35" />
      </div>
      <div class="modal-footer center">
        <button type="button" class="btn btn-primary" ng-click="editBackground()" >редактировать</button>
      </div>
    </div>
  </div>
</div>
