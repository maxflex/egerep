angular.module 'Egerep'
    .service 'AccessService', ($rootScope, $timeout) ->
        this.unlocked = []

        this.serve = (account_id, callback, param) ->
            confirm_hash = 'cbcb58ac2e496207586df2854b17995f'
            bootbox.prompt {
                title: "Введите пароль",
                className: "modal-password",
                callback: (result) =>
                    if result isnt null
                        if md5(result) is confirm_hash
                            if this.isLocked account_id
                                if not param or param.confirmed
                                    this.unlock account_id
                                else
                                    this.lock account_id
                            else
                                if param and param.confirmed
                                    this.unlock account_id
                                else
                                    this.lock account_id

                            callback param if callback
                            $timeout ->
                                $rootScope.$apply()

                            return true
                        else
                            $('.bootbox-form').addClass('has-error').children().first().focus()
                            $('.bootbox-input-text').on 'keydown', ->
                                $(this).parent().removeClass 'has-error'
                            return false
                ,
                buttons: {
                    confirm: {
                        label: "Подтвердить"
                    },
                    cancel: {
                        className: "display-none"
                    },
                }
                onEscape: true
            }
            return

        this.lock = (account_id) ->
            delete this.unlocked[account_id]

        this.unlock = (account_id) ->
            this.unlocked[account_id] = true

        this.isLocked = (account_id) ->
            return !this.unlocked[account_id]

        this.isUnlocked = (account_id) ->
            return this.unlocked[account_id]

        this