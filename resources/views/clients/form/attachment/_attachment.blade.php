<div ng-if="selected_attachment">
<div class="row mb">
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
        <div class="form-group" id='forecast'>
            <input type="text" class="form-control digits-only" placeholder="прогноз в неделю" ng-model='selected_attachment.forecast'>
        </div>
    </div>
    <div class="col-sm-6">
        <div class='mbs'>
            <b>Стыковку создал:</b> @{{ selected_attachment.user_login }} @{{ formatDateTime(selected_attachment.created_at) }}
        </div>
        <div class='mbs'>
            <b>Статус стыковки:</b>
            {{-- #859 --}}
            @{{ AttachmentService.getStatus(selected_attachment) }}
        </div>
        <div class='mbs'>
            <b>Видимость:</b>
            <span ng-click="deny(selected_attachment, 'hide')" class="link-like">
                @{{ AttachmentVisibility[selected_attachment.hide] }}
            </span>
        </div>
        <div class="mbs">
            <b>Прозвонен через 2 дня:</b>
            <span class="link-like" ng-click="deny(selected_attachment, 'called')">@{{ YesNo[selected_attachment.called] }}</span>
        </div>
        <div class='mbs'>
            <b>Проведено занятий:</b> @{{ selected_attachment.account_data_count }}
        </div>
    </div>
</div>
