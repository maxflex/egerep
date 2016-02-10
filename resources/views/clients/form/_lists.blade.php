<div class="row controls-line">
    <div class="col-sm-12">
        <span
            ng-repeat="subject_id in client.subject_list"
            ng-class="{'link-like': selected_list_id != subject_id}"
            ng-click="setList(subject_id)"
        >список по @{{ Subjects.dative[subject_id] }}</span>
        <span class="link-like link-gray" ng-click="dialog('add-subject')">добавить список</span>
        <a class="text-danger" href="#">удалить список</a>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <p ng-repeat="tutor_id in client.lists[selected_list_id]">
            <span class="line-number">@{{ tutor_id }}</span>
            <a href="tutors/@{{ tutor_id }}/edit">@{{ tutors[tutor_id] }}</a>
            <span ng-show="!attachmentExists(selected_list_id, tutor_id)"
                class="link-like link-gray" style="margin-left: 10px"
                ng-click="newAttachment(tutor_id, selected_list_id)">начать процесс</span>
        </p>
        <p>
            <span class="line-number"></span>
            <span class="link-like" ng-click="dialog('add-tutor')">добавить репетитора</span>
        </p>
    </div>
</div>
