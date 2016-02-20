<div ng-show="selected_list.attachments.length">
    <div class="row controls-line">
        <div class="col-sm-12">
            <span
                ng-repeat="attachment in selected_list.attachments"
                ng-class="{'link-like': attachment !== selected_attachment}"
                ng-click="selectAttachment(attachment)"
            >@{{ tutors[attachment.tutor_id] }}</span>
            <span class='link-like text-danger show-on-hover' ng-click="removeAttachment()" ng-show='selected_attachment'>удалить стыковку</span>
        </div>
    </div>

        <div ng-if="selected_attachment">
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <input type="text" placeholder="дата стыковки"
                        class="form-control bs-date" ng-model="selected_attachment.attachment_date">
                </div>
                <div class="form-group">
                    <select class="form-control" ng-model='selected_attachment.grade'
                        ng-options='+(grade_id) as label for (grade_id, label) in Grades'>
                        <option value="">выберите класс</option>
                    </select>
                </div>
                <div class="form-group">
                    <select id="sp-attachment-subjects"
                      multiple
                      ng-model="selected_attachment.subjects"
                      ng-options="index as subject for (index, subject) in Subjects.all">
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <textarea style="height: 75px" cols="40" class="form-control"></textarea>
                </div>
                <div class="form-group"><input type="text" class="form-control digits-only" placeholder="прогноз в неделю"></div>
            </div>
            <div class="col-sm-6">
                <p>
                    <b>Стыковку создал:</b> root 15.09.16 в 16:15
                </p>
                <p>
                    <b>Статус стыковки:</b> новая
                </p>
            </div>
        </div>

        <div class="row mb">
            <div class="col-sm-12">
                <span class="pointer no-margin-right comment-add" style="font-size: 12px">комментировать</span>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <b>архивация</b>
                </div>
            </div>
        </div>

        <div class="row mb">
            <div class="col-sm-3">
                <div class="form-group">
                    <input type="text" class="form-control bs-date" placeholder="дата архивации"
                        ng-model="selected_attachment.archive_date">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control digits-only" placeholder="всего занятий не проведено"
                        ng-model='selected_attachment.total_lessons_missing'>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <textarea style="height: 75px" cols="40" class="form-control"></textarea>
                </div>
            </div>
            <div class="col-sm-6">

            </div>
        </div>


        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <b>отзыв</b>
                </div>
            </div>
        </div>

        <div class="row mb">
            <div class="col-sm-3">
                <div class="form-group"><input type="text" class="form-control ditits-only" placeholder="оценка репетитору"></div>
                <div class="form-group"><input type="text" class="form-control" placeholder="подпись"></div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <textarea style="height: 75px" cols="40" class="form-control"></textarea>
                </div>
            </div>
            <div class="col-sm-6">

            </div>
        </div>
    </div>
</div>
