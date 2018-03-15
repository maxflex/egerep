<div style='width: 50px'>
    <table class="accounts-data">
        <tr>
            <td style='border: none; padding: 0'>
                <div style="position:relative; margin-bottom: 20px">
                    <div class="account-guard" ng-show="account.confirmed && !{{ allowed(\Shared\Rights::ER_EDIT_ACCOUNTS, true) }}"></div>
                    <div class="mbs">
                        <span>Итого комиссия за период</span>
                        @{{ totalCommission(account) }}
                    </div>
                    <div class="mbs">
                        <span>Дебет</span>
                        <span>@{{ account.debt_calc }}</span>
                    </div>
                    <div class="mbs" style='position: relative'>
                        @if(! allowed(\Shared\Rights::ER_EDIT_PAYMENTS))
                            <div class="blocker-div" style='height: calc(100% + 5px)'></div>
                        @endif
                        <span>Задолженность</span>
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
                        <div class="blocker-div" ng-show="account.confirmed && !{{ allowed(\Shared\Rights::ER_EDIT_ACCOUNTS, true) }}"></div>
                        <span>Расчет создан</span>
                        @{{ account.user_login }} @{{ formatDateTime(account.created_at) }}
                        <span class="link-like text-danger" style='margin-left: 8px'
                            ng-hide="account.all_payments.length"
                            ng-click="remove(account)">удалить встречу</span>
                    </div>
                    <div class="mbs">
                        <span>Статус проводки</span>
                        <span @if(allowed(\Shared\Rights::ER_EDIT_ACCOUNTS)) class="link-like" ng-click="toggleConfirmed(account, Account)" @endif
                            ng-class="{
                                'text-danger': !account.confirmed,
                                'text-success': account.confirmed
                            }">
                            @{{ Confirmed[account.confirmed] }}
                        </span>
                    </div>
                </div>
                <div class="account-payments" style='position: relative'>
                    <div ng-repeat='payment in account.all_payments'>
                        <span ng-show='payment.id' ng-click='paymentModal(account, payment)' class='text-success default' ng-class="{
                            'link-like': (!payment.confirmed || user.rights.indexOf('48') !== -1)
                        }">платеж</span>
                        <span ng-show='!payment.id'>платеж</span>
                        на сумму @{{ payment.sum }} руб.
                        (@{{ payment.id ? PaymentMethods[payment.method] : 'взаимозачёт' }})
                        на дату @{{ payment.date }}
                        проведён @{{ UserService.getLogin(payment.user_id) }} @{{ formatDateTime(payment.created_at) }}

                        <span style='margin-left: 8px' @if(allowed(\Shared\Rights::ER_EDIT_PAYMENTS)) class="link-like" ng-click="toggleConfirmed(payment, AccountPayment)" @endif
                            ng-class="{
                                'link-like': (payment.id && user.rights.indexOf('48') !== -1),
                                'text-danger': !payment.confirmed,
                                'text-success': payment.confirmed
                            }">
                            @{{ Confirmed[payment.confirmed] }}
                        </span>
                    </div>
                    <div>
                        <span style='font-weight: normal' class='link-like' ng-click='paymentModal(account)'>добавить платеж</span>
                    </div>
                </div>
                <div style='margin: 20px 0 10px; position: relative'>
                    <comments entity-type='account' entity-id='account.id' user='user'></comments>
                </div>
            </td>
        </tr>
    </table>
</div>