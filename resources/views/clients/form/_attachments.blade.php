<div ng-show="selected_list.attachments.length">
    <div class="row controls-line">
        <div class="col-sm-12">
            <span
                ng-repeat="attachment in selected_list.attachments"
                ng-class="{'link-like': attachment !== selected_attachment}"
                ng-click="selectAttachment(attachment)"
            >@{{ tutors[attachment.tutor_id] }}</span>
            <a class="text-danger" href="#">удалить стыковку</a>
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
                    <select class="form-control" ng-model="selected_attachment.grade" ng-options="grade as (grade + ' класс') for grade in [9, 10, 11]">
                        <option value="">класс</option>
                    </select>
                </div>
                <div class="form-group">
                    <select id="sp-attachment-subjects"
                      multiple
                      ng-model="selected_attachment.subjects"
                      ng-options="index as subject for (index, subject) in Subjects.all">
                    </select>

                    {{-- <ui-select ng-model='selected_attachment.subjects'>
                      <ui-select-match>
                        <span ng-bind='selected_attachment.subjects'></span>
                      </ui-select-match>
                      <ui-select-options ng-repeat='(index, subject) in Subjects.all track by $index'>
                        <span ng-bind='subject'></span>
                      </ui-select-options>
                    </ui-select> --}}
                    {{-- <ui-select multiple ng-model="selected_attachment.subjects">
                        <ui-select-match>
                            @{{ Subjects.all[$item.index] }}
                        </ui-select-match>
                        <ui-select-choices repeat="subject.index as (index, subject) in Subjects.all">
                            @{{ subject.value }}
                        </ui-select-choices>
                    </ui-select> --}}

                    {{-- <select id='attachment-subjects'
                        multiple
                        ng-model="selected_attachment.subjects"
                        ng-options="index as subject for (index, subject) in Subjects.all"
                    > --}}

                    {{-- <ol class="nya-bs-select" ng-model="selected_attachment.subjects" multiple>
                        <li nya-bs-option="index as subject for (index, subject) in Subjects.all">
                            <a>@{{subject}}</a>
                        </li>
                    </ol> --}}

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
                <div class="form-group"><input type="text" class="form-control bs-date" placeholder="дата архивации"></div>
                <div class="form-group"><input type="text" class="form-control digits-only" placeholder="всего занятий не проведено"></div>
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
                <div class="form-group"><input type="text" class="form-control bs-date" placeholder="дата архивации"></div>
                <div class="form-group"><input type="text" class="form-control digits-only" placeholder="всего занятий не проведено"></div>
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
