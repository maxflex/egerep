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
                    Клиент: @{{ selected_client.address ? selected_client.address : 'Адрес не заполнен' }}<span ng-repeat="phone in selected_client.phones">,
                        <span class="underline-hover inline-block"
                            ng-click="PhoneService.call(popup_attachment.client[phone_field])"
                            ng-class="{'phone-duplicate-new': popup_attachment.client.duplicate}"
                        >@{{ PhoneService.format(phone) }}</span></span>
                </div>
                <div class="mbs">
                    Ученик: <a href='@{{ selected_client.link }}'>@{{ popup_attachment.client.name | cut:true:100:'без имени' }}</a>, текущий класс - @{{ popup_attachment.client.grade ? Grades[popup_attachment.client.grade] : 'без класса' }}
                </div>
                <div class="mbs">Стыковка: <span class="text-success">@{{ popup_attachment.date }}</span>, @{{ popup_attachment.comment }}</div>
                <div class="mbs">
                    Проведено занятий: @{{ selected_client.total_lessons > 0 ? selected_client.total_lessons : (selected_client.total_lessons + selected_client.total_lessons_missing > 0 ? '' : 'занятий нет') }}<span style='text-gray'><span ng-show='selected_client.total_lessons > 0 && selected_client.total_lessons_missing > 0' class='text-gray'>+</span><span ng-show='selected_client.total_lessons_missing' class="text-gray">@{{ selected_client.total_lessons_missing }}</span></span>
                </div>
                <div class="mbs">Прогноз:
                    <span ng-show='selected_client.forecast'>@{{ selected_client.forecast | number }} руб./неделя</span>
                    <span ng-hide='selected_client.forecast'>не установлен</span>
                </div>
                <div ng-if='popup_attachment.archive'>
                    Архивации:
                    <span class="text-danger">@{{ popup_attachment.archive.date }}</span>, @{{ popup_attachment.archive.comment }}
                </div>
                <div ng-if='popup_attachment.archive'>
                    Разархивация:
                    <span class='inline-fixed-width' ng-click="updateArchive('state', ArchiveStates)">@{{ ArchiveStates[popup_attachment.archive.state] }}</span>
                    <span class='inline-fixed-width' ng-click="updateArchive('checked', Checked)" ng-class="+popup_attachment.archive.checked ? 'text-raw' : 'text-danger'">@{{ Checked[popup_attachment.archive.checked] }}</span>
                </div>

                <div style="margin-top: 30px">
                    <comments entity-type='attachment' entity-id='popup_attachment.id' user='user'></comments>
                </div>
            </div>
        </div>
    </div>
</div>
