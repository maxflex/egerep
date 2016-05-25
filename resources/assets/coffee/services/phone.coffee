angular.module 'Egerep'
    .service 'PhoneService', ($rootScope, $http) ->
        # позвонить
        this.call = (number) ->
            location.href = "sip:" + number.replace(/[^0-9]/g, '')

        this.isMobile = (number) ->
            parseInt(number[4]) is 9 or parseInt(number[1]) is 9

        this.clean = (number) ->
            number.replace /[^0-9]/gim, "";

        this.format = (number) ->
            number = this.clean number
            '+'+number.substr(0,1)+' ('+number.substr(1,3)+') '+number.substr(4,3)+'-'+number.substr(7,2)+'-'+number.substr(9,2)

        this.sms = (number) ->
            $rootScope.sms_number = number
            $('#sms-modal').modal 'show'
        this
