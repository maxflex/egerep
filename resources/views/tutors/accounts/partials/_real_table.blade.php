{{-- REAL DATES --}}
<div ng-if='tutor.last_accounts.length > 0' ng-init='i = 0'>
    <table class='accounts-table'>
        <thead class="high-z-index small">
            <tr>
                <td class='empty-td centered'>
                    <div class='mbs'>&nbsp;</div>
                    <div class='mbs'>&nbsp;</div>
                    <span class='link-like' ng-hide='all_displayed' ng-click='loadPage()'>
                        @{{ left == 1 ? 'все время' : '+1 период'}}
                    </span>
                </td>
            </tr>
        </thead>
        <tbody ng-repeat='account in tutor.last_accounts'>
            <tr ng-repeat='date in getDates($index)' class="tr-@{{ date }}">
                <td class='date-td' ng-class="{'double-border-bottom': getDay(date) == 6}">
                    @{{ formatDate(date) }}
                    <span class="text-gray small" style='margin: 0 5px'>@{{ Weekdays[getDay(date)] }}</span>
                </td>
            </tr>
            <tr>
                <td class='invisible-td small'>в периоде</td>
            </tr>
            <tr>
                {{-- 21px – высота одного DIV.mbs (16px + 5px margin-bottom) --}}
                {{-- 20px – padding TD --}}
                {{-- 25px высота одного payment (+1 – это кнопка «добавить»)--}}
                {{-- +5px – высоты не сходились --}}
                <td class="period-end" ng-style="{'height': (7 * 21) + 20 + (25 * (account.all_payments.length + 1)) + 5 + 'px'}">
                </td>
            </tr>
        </tbody>
    </table>

    <div class="right-table-scroll">
        <table class='accounts-table'>
            <thead class='small' ng-repeat-start='account in tutor.last_accounts' ng-if='$index == 0'>
                <tr>
                    @include('tutors.accounts.partials._client_info_cell')
                </tr>
            </thead>
            <tbody ng-repeat-end>
                <tr ng-repeat='date in getDates($index)' class='tr-@{{ date }}'>
                    <td ng-if='!clients.length' class="fake-cell"></td>
                    <td ng-repeat='client in clients' ng-class="{
                        'attachment-start': date == client.attachment_date,
                        'archive-date': date == client.archive_date,
                        'double-border-bottom': getDay(date) == 6
                    }">
                        <input type="text" class='account-column no-border-outline' id='i-@{{ date }}-@{{ $index }}'
                            ng-focus='selectRow(date)'
                            ng-blur='deselectRow(date)'
                            ng-keyup='periodsCursor(date, $index, $event, account.data[client.id], date)'
                            ng-class="{
                                'attachment-start': date == client.attachment_date,
                                'archive-date': date == client.archive_date,
                            }"
                            ng-model='account.data[client.id][date]' title='@{{ formatDate(date) }}'>
                    </td>
                </tr>
                <tr>
                    <td ng-if='!clients.length' class="fake-cell"></td>
                </tr>
                <tr>
                    <td ng-if='!clients.length' class="fake-cell"></td>
                    <td ng-repeat='client in clients' class="invisible-td small" style='text-align: center'>
                        @{{ periodLessons(account, client.id) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="10" class='period-end'>
                        <table class="accounts-data">
                            <tr>
                                <td style='border: none; padding: 0'>
                                    <div style="position:relative;">
                                        <div class="account-guard" ng-show="account.confirmed && !{{ allowed(\Shared\Rights::ER_EDIT_ACCOUNTS, true) }}"></div>
                                        <div class="mbs">
                                            <span>Итого комиссия за период (руб.):</span>
                                            @{{ totalCommission(account) }}
                                        </div>
                                        <div class="mbs">
                                            <span>Дебет:</span>
                                            <span>@{{ account.debt_calc }}</span>
                                        </div>
                                        <div class="mbs">
                                            <span>Задолженность:</span>
                                            <pencil-input model='account.debt'></pencil-input>
                                            <span ng-if='account.debt > 0'> – репетитор <span class="link-like-no-color"
                                                                                                  ng-class="{
                                                       'text-danger': account.debt_type == 0,
                                                       'text-success': account.debt_type == 1,
                                                   }"
                                                       ng-click="toggleEnum(account, 'debt_type', DebtTypes)">@{{ DebtTypes[account.debt_type] }}</span>
                                           </span>
                                        </div>
                                        <div class="mbs">
                                            <span>Комментарий:</span>
                                            <pencil-input model='account.comment' class='period-comment'></pencil-input>
                                        </div>
                                        <div class="mbs">
                                            <span>Расчет создан:</span>
                                            @{{ account.user_login }} @{{ formatDateTime(account.created_at) }}
                                        </div>
                                        <div class="mbs" style='position: relative'>
                                            <div class="blocker-div" ng-show="account.confirmed && !{{ allowed(\Shared\Rights::ER_EDIT_ACCOUNTS, true) }}"></div>
                                            <span>Действия:</span>
                                            <span class="link-like margin-right" ng-click="changeDateDialog($index)">изменить дату встречи</span>
                                            <span class="link-like text-danger margin-right"  ng-click="remove(account)">удалить встречу</span>
                                        </div>
                                    </div>
                                    <div class="mbs">
                                        <span>Статус проводки:</span>
                                        <span @if(allowed(\Shared\Rights::ER_EDIT_ACCOUNTS)) class="link-like" ng-click="toggleConfirmed(account)" @endif
                                              ng-class="{
                                                  'text-danger': !account.confirmed,
                                                  'text-success': account.confirmed
                                              }">
                                            @{{ Confirmed[account.confirmed] }}
                                        </span>
                                    </div>
                                    <div class="mbs">
                                        <span>Платежи:</span>
                                        <table class='account-payments small'>
                                            <tr ng-repeat='payment in account.all_payments'>
                                                <td width='80'>@{{ payment.sum }} руб.</td>
                                                <td width='130'>@{{ payment.id ? PaymentMethods[payment.method] : 'взаимозачёт' }}</td>
                                                <td width='80'>@{{ payment.date }}</td>
                                                <td width='100'>
                                                    <span @if(allowed(\Shared\Rights::EDIT_PAYMENTS)) class="link-like" ng-click="togglePaymentConfirmed(payment)" @endif
                                                          ng-class="{
                                                              'text-danger': !payment.confirmed,
                                                              'text-success': payment.confirmed
                                                          }">
                                                        @{{ Confirmed[payment.confirmed] }}
                                                    </span>
                                                </td>
                                                <td width='100'>
                                                    <span ng-show='payment.id' ng-click='paymentModal(account, payment)' class='link-like'>редактировать</span>
                                                </td>
                                                <td width='60'>
                                                    <span ng-show='payment.id' ng-click='removePayment(account, payment)' class='link-like text-danger'>удалить</span>
                                                </td>
                                                <td>
                                                    @{{ UserService.getLogin(payment.user_id) }} @{{ formatDateTime(payment.created_at) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan='4'>
                                                    <span style='font-weight: normal' class='link-like' ng-click='paymentModal(account)'>добавить платеж</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- ДОБАВЛЕНИЕ ПЛАТЕЖА --}}
<div id="add-account-payment" class="modal" role="dialog" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@{{ modal_payment.id ? 'Редактировать' : 'Добавить' }} платеж</h4>
      </div>
      <div class="modal-body">
          <div class="form-group">
              <input placeholder='передано' type="text" ng-model='modal_payment.sum' class="form-control digits-only">
          </div>
          <div class="form-group">
              <select ng-model='modal_payment.method' class='form-control'>
                  <option value="">метод расчета</option>
                  <option disabled>──────────────</option>
                  <option ng-repeat='(index, method) in PaymentMethods' value="@{{ index }}"
                    ng-selected="modal_payment.method !== undefined && modal_payment.method == index">
                      @{{ method }}
                  </option>
              </select>
          </div>
          <div class="form-group">
              <input placeholder='дата' type="text" ng-model='modal_payment.date' class="form-control bs-date">
          </div>
      </div>
      <div class="modal-footer center">
        <button type="button" class="btn btn-primary" ng-click="editPayment()">
            @{{ modal_payment.id ? 'редактировать' : 'добавить' }}
        </button>
      </div>
    </div>
  </div>
</div>
