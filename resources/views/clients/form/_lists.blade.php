<div class="row controls-line">
    <div class="col-sm-12">
        <span
            ng-repeat="list in selected_request.lists"
            ng-class="{'link-like': selected_list !== list}"
            ng-click="setList(list)"
        >список по <sbj ng-repeat='subject_id in list.subjects'>@{{Subjects.dative[subject_id]}}@{{$last ? '' : ' и '}}</sbj></span>
        <span class="link-like link-gray" ng-click="list_subject_id = 0; dialog('add-subject')">добавить список</span>
        <span class="link-like text-danger show-on-hover" ng-show='selected_list' ng-click="removeList()">удалить список</span>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <p ng-repeat="tutor_id in selected_list.tutor_ids">
            <span class="line-number">@{{ tutor_id }}</span>
            <a href="tutors/@{{ tutor_id }}/edit">@{{ tutors[tutor_id] }}</a>
            <span ng-hide="attachmentExists(tutor_id)"
                class="link-like link-gray" style="margin-left: 10px"
                ng-click="newAttachment(tutor_id)">начать процесс</span>
        </p>
        <p ng-show='selected_list'>
            <span class="line-number"></span>
            <span class="link-like" ng-click="dialog('add-tutor')">добавить репетитора</span>
        </p>
    </div>
</div>
