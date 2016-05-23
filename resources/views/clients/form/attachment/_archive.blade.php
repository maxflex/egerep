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
        <div class='mbs'>
            <b>Процесс заархивирован:</b> @{{ selected_attachment.archive.user_login }} @{{ formatDateTime(selected_attachment.archive.created_at) }}
        </div>
        <div class='mbs'>
            <b>Разархивация и продолжение:</b> <span class="link-like"
                ng-click="toggleEnum(selected_attachment.archive, 'state', ArchiveStates)">@{{ ArchiveStates[selected_attachment.archive.state] }}</span>
        </div>
    </div>

    <div class="col-sm-12">
        <comments entity-type='archive' entity-id='selected_attachment.id' user='{{ $user }}'></comments>
    </div>
</div>
