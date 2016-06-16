{{-- ДОБАВЛЕНИЕ РАСЧЕТА --}}
<div class="modal" id='add-account' tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Добавление расчета</h4>
            </div>
            <div class="modal-body">
                <input type="text" id='date-end' class="form-control" placeholder="дата конца периода" ng-model='new_account_date_end'>
            </div>
            <div class="modal-footer center">
                <button type="button" class="btn btn-primary" ng-disabled="!new_account_date_end" ng-click="addAccount()">Добавить</button>
            </div>
        </div>
    </div>
</div>


{{-- ИЗМЕНЕНИЕ ДАТЫ ВСТРЕЧИ --}}
<div class="modal" id='change-account-date' tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Изменить дату встречи</h4>
            </div>
            <div class="modal-body">
                <input type="text" id='date-end-change' class="form-control" placeholder="дата конца периода" ng-model='change_date_end'>
            </div>
            <div class="modal-footer center">
                <button type="button" class="btn btn-primary" ng-click="changeDate()">Изменить</button>
            </div>
        </div>
    </div>
</div>

{{-- ПАРАМЕТРЫ СТЫКОВКИ --}}
<div class="modal" id='account-info' tabindex="-1">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content" style="height: 50%">
            <div class="div-loading" ng-show='!popup_attachment'>
                <span>загрузка...</span>
            </div>
            <div class="modal-body" style='overflow: scroll; max-height: 100%'>
                <div class="mbs">
                    Ученик: <a href='@{{ selected_client.link }}'>@{{ popup_attachment.client.name | cut:true:100:'имя не указано' }}</a>, @{{ popup_attachment.client.grade ? Grades[popup_attachment.client.grade] : 'класс не указан' }},
                    <span ng-repeat="phone_field in ['phone', 'phone2', 'phone3', 'phone4']">
                        <span ng-show="popup_attachment.client[phone_field]">
                            <span class="underline-hover inline-block"
                                  ng-click="PhoneService.call(popup_attachment.client[phone_field])"
                                  ng-class="{'phone-duplicate-new': popup_attachment.client.duplicate}"
                            >
                                  @{{ PhoneService.format(popup_attachment.client[phone_field]) }}</span>
                        </span>
                    </span>
                </div>
                <div class="mbs">Дата стыковки: <span class="text-success bold">@{{ popup_attachment.date }}</span>, @{{ popup_attachment.comment }}</div>
                <div class="mbs">
                    Проведено занятий:
                    @{{ selected_client.total_lessons > 0 ? selected_client.total_lessons : null }}<span style='text-gray'><span ng-show='selected_client.total_lessons > 0 && selected_client.total_lessons_missing > 0' class='text-gray'>+</span><span ng-show='selected_client.total_lessons_missing' class="text-gray">@{{ selected_client.total_lessons_missing }}</span></span>
                </div>
                <div class="mbs">Прогноз:
                    <span ng-show='selected_client.forecast'>@{{ selected_client.forecast | number }} руб./неделя</span>
                    <span ng-hide='selected_client.forecast'>не указан</span>
                </div>
                <div ng-if='popup_attachment.archive'>
                    Дата архивации:
                    <span class="text-danger bold">@{{ popup_attachment.archive.date }}</span>, @{{ popup_attachment.archive.comment }}
                </div>

                <div style="margin-top: 30px">
                    <comments entity-type='attachment' entity-id='popup_attachment.id' user='user'></comments>
                </div>
            </div>
        </div>
    </div>
</div>
