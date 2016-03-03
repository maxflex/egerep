<div class="row controls-line">
    <div class="col-sm-12">
        <span
            ng-repeat="list in selected_request.lists"
            ng-class="{'link-like': selected_list !== list}"
            ng-click="setList(list)"
        >список по <sbj ng-repeat='subject_id in list.subjects'>@{{Subjects.dative[subject_id]}}@{{$last ? '' : ' и '}}</sbj></span>
        <span class="link-like link-gray" ng-click="addList()">добавить список</span>
        <div class="teacher-remove-droppable drop-delete" ng-show='is_dragging_teacher'
            ui-sortable="dropzone" ng-model="tutor_ids_removed">удалить из списка</div>
        <span class="link-like text-danger show-on-hover" ng-show='selected_list' ng-click="removeList()">удалить список</span>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div ui-sortable='sortableOptions' ng-model="selected_list.tutor_ids" class="mbs">
            <div class='mbs' ng-repeat="tutor_id in selected_list.tutor_ids" data-id='@{{tutor_id}}'>
                <a href="tutors/@{{ tutor_id }}/edit">@{{ tutors[tutor_id] }}</a>
                <span ng-hide="attachmentExists(tutor_id)"
                    class="link-like link-gray" style="margin-left: 10px"
                    ng-click="newAttachment(tutor_id)">начать процесс</span>
            </div>
        </div>
        <p ng-show='selected_list'>
            <a class="link-gray" href='tutors/add/@{{ selected_list.id }}'>добавить репетитора</a>
        </p>
    </div>
</div>
