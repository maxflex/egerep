angular.module 'Egerep'
    .service 'PhoneService', ($http) ->
        # позвонить
        this.call = (number) ->
            location.href = "sip:" + number.replace(/[^0-9]/g, '')

        this
