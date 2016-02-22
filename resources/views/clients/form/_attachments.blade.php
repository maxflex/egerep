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
                    <div class="input-group custom">
                      <span class="input-group-addon">дата стыковки –</span>
                      <input type="text"
                          class="form-control bs-date" ng-model="selected_attachment.date">
                    </div>
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
                    <textarea style="height: 75px" cols="40" class="form-control"
                        ng-model='selected_attachment.comment'></textarea>
                </div>
                <div class="form-group"><input type="text" class="form-control digits-only" placeholder="прогноз в неделю"></div>
            </div>
            <div class="col-sm-6">
                <p>
                    <b>Стыковку создал:</b> @{{ selected_attachment.user_login }} @{{ formatDateTime(selected_attachment.created_at) }}
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
                    <span ng-hide='selected_attachment.archive' class="link-like link-gray" style="margin-left: 10px"
                        ng-click="toggleArchive()">начать процесс</span>
                    <span ng-show='selected_attachment.archive' class="link-like link-gray" style="margin-left: 10px"
                        ng-click="toggleArchive()">разархивировать</span>
                </div>
            </div>
        </div>

        <div class="row mb" ng-if='selected_attachment.archive'>
            <div class="col-sm-3">
                <div class="form-group">
                    <div class="form-group">
                        <div class="input-group custom">
                          <span class="input-group-addon">дата архивации –</span>
                          <input type="text" class="form-control bs-date"
                              ng-model="selected_attachment.archive.date">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control digits-only" placeholder="всего занятий не проведено"
                        ng-model='selected_attachment.archive.total_lessons_missing'>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <textarea style="height: 75px" cols="40" class="form-control" ng-model='selected_attachment.archive.comment'></textarea>
                </div>
            </div>
            <div class="col-sm-6">
                <p>
                    <b>Процесс заархивирован:</b> @{{ selected_attachment.archive.user_login }} @{{ formatDateTime(selected_attachment.archive.created_at) }}
                </p>
                <p>
                    <b>Разархивация и продолжение:</b> <span class="link-like"
                        ng-click="toggleEnum(selected_attachment.archive, 'state', ArchiveStates)">@{{ ArchiveStates[selected_attachment.archive.state] }}</span>
                </p>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <b>отзыв</b>
                    <span ng-hide='selected_attachment.review' class="link-like link-gray" style="margin-left: 10px"
                        ng-click="toggleReview()">написать отзыв</span>
                    <span ng-show='selected_attachment.review' class="link-like link-gray" style="margin-left: 10px"
                        ng-click="toggleReview()">удалить отзыв</span>
                </div>
            </div>
        </div>

        <div class="row mb" ng-if='selected_attachment.review'>
            <div class="col-sm-3">
                <div class="form-group">
                    <select class="form-control" ng-model='selected_attachment.review.score'
                        ng-options='state_id as label for (state_id, label) in ReviewScores'>
                        <option value="">оценка репетитору</option>
                    </select>
                </div>
                <div class="form-group"><input type="text" class="form-control" ng-model='selected_attachment.review.signature' placeholder="подпись"></div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <textarea style="height: 75px" cols="40" class="form-control" ng-model='selected_attachment.review.comment'></textarea>
                </div>
            </div>
            <div class="col-sm-6">
                <p>
                    <b>Отзыв создан:</b> @{{ selected_attachment.review.user_login }} @{{ formatDateTime(selected_attachment.review.created_at) }}
                </p>
                <p>
                    <b>Статус:</b> <span class="link-like"
                        ng-click="toggleEnum(selected_attachment.review, 'state', ReviewStates)">@{{ ReviewStates[selected_attachment.review.state] }}</span>
                </p>
            </div>
        </div>
    </div>
</div>
