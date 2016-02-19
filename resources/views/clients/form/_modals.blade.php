{{-- ДОБАВЛЕНИЕ СПИСКА --}}
<div id="add-subject" class="modal" role="dialog" tabindex="-1" ng-if="selected_request">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Добавление списка</h4>
      </div>
      <div class="modal-body">
        <select class='form-control' multiple ng-model='$parent.list_subject_id' id='sp-subject-select-model'
            ng-options='subject_id as subject_name
                        for (subject_id, subject_name) in Subjects.all'
        >
        </select>

      </div>
      <div class="modal-footer center">
        <button type="button" class="btn btn-primary" ng-click="addListSubject()"
            ng-disabled="!list_subject_id">Добавить</button>
      </div>
    </div>
  </div>
</div>



{{-- ДОБАВЛЕНИЕ РЕПЕТИТОРА --}}
<div id="add-tutor" class="modal" role="dialog" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Добавление репетитора</h4>
      </div>
      <div class="modal-body">
        <select class='form-control' ng-model='list_tutor_id'
            ng-options='tutor_id as tutor_name
                        for (tutor_id, tutor_name) in tutors'
        >
            <option value="">выберите преподавателя</option>
        </select>

      </div>
      <div class="modal-footer center">
        <button type="button" class="btn btn-primary" ng-click="addListTutor()"
            ng-disabled="!list_tutor_id">Добавить</button>
      </div>
    </div>
  </div>
</div>
