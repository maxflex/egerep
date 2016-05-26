angular.module('Egerep')
    .filter 'cut', ->
      (value, wordwise, max, tail) ->
        if !value
          return 'имя не указано'
        max = parseInt(max, 10)
        if !max
          return value
        if value.length <= max
          return value
        value = value.substr(0, max)
        if wordwise
          lastspace = value.lastIndexOf(' ')
          if lastspace != -1
            #Also remove . and , so its gives a cleaner result.
            if value.charAt(lastspace - 1) == '.' or value.charAt(lastspace - 1) == ','
              lastspace = lastspace - 1
            value = value.substr(0, lastspace)
        value + (tail or '…')
    .controller 'AccountsCtrl', ($rootScope, $scope, $http, $timeout, Account, PaymentMethods, DebtTypes, AccountPeriods, Grades) ->
        $scope.PaymentMethods = PaymentMethods
        $scope.DebtTypes = DebtTypes
        $scope.AccountPeriods = AccountPeriods
        $scope.Grades = Grades
        $scope.current_scope = $scope
        $scope.current_period = 0

        angular.element(document).ready ->
            $scope.loadPage()

        $scope.loadPage = (type) ->
            $rootScope.frontend_loading = true
            $http.get "api/accounts/#{$scope.tutor_id}?type=#{AccountPeriods[$scope.current_period]}"
            .success (response) ->
                    renderData(response)
                    $scope.current_period++

        renderData = (data) ->
            $scope.tutor = data.tutor
            $scope.date_limit = data.date_limit
            $rootScope.frontend_loading = false
            $('.accounts-table').stickyTableHeaders('destroy')
            $timeout ->
                $('.accounts-table').stickyTableHeaders()

                $('.right-table-scroll').scroll ->
                    $(window).trigger('resize.stickyTableHeaders')

        getAccountStartDate = (index) ->
            if index > 0
                moment($scope.tutor.last_accounts[index - 1].date_end).add(1, 'days').toDate()
            else
                new Date $scope.date_limit

        getAccountEndDate = (index) ->
            if (index + 1) is $scope.tutor.last_accounts.length
                ''
            else
                moment($scope.tutor.last_accounts[index + 1].date_end).subtract(1, 'days').toDate()

        $scope.changeDateDialog = (index) ->
            $('#date-end-change').datepicker('destroy')
            $('#date-end-change').datepicker
                language	: 'ru'
                autoclose	: true
                orientation	: 'bottom auto'
                startDate   : getAccountStartDate(index)
                endDate     : getAccountEndDate(index)

            $scope.selected_account = $scope.tutor.last_accounts[index]
            $scope.change_date_end = $scope.formatDate($scope.selected_account.date_end, true)
            $scope.dialog 'change-account-date'

        $scope.changeDate = ->
            $scope.selected_account.date_end = convertDate($scope.change_date_end)
            Account.update
                id: $scope.selected_account.id
                date_end: $scope.selected_account.date_end
            # , (response) ->
            #     $scope.selected_account = response
            $scope.closeDialog 'change-account-date'

        $scope.remove = (account) ->
            bootbox.confirm 'Удалить встречу?', (result) ->
                if result is true
                    Account.delete {id: account.id}, ->
                        $scope.tutor.accounts = removeById($scope.tutor.accounts, account.id)


        $scope.save = ->
            $.each $scope.tutor.last_accounts, (index, account) ->
                Account.update account

        $scope.getFakeDates = ->
            dates = []
            current_date = moment().subtract(60, 'days').format('YYYY-MM-DD')
            while current_date <= moment().format('YYYY-MM-DD')
                dates.push current_date
                current_date = moment(current_date).add(1, 'days').format('YYYY-MM-DD')
            dates


        $scope.getDates = (index) ->
            dates = []
            # если нулевой элемент, то отсчитываем от даты первой стыковки (самой ранней стыковки)
            # иначе отсчитываем от даты конца предыдущего периода
            if not index
                current_date = moment($scope.date_limit).format('YYYY-MM-DD')
            else
                current_date = moment($scope.tutor.last_accounts[index - 1].date_end).add(1, 'days').format('YYYY-MM-DD')

            while (current_date <= $scope.tutor.last_accounts[index].date_end)
                dates.push current_date
                current_date = moment(current_date).add(1, 'days').format('YYYY-MM-DD')
            dates

        # откуда можно выбирать дату в календаре
        getCalendarStartDate = ->
            if $scope.tutor.last_accounts.length > 0
                date_end = $scope.tutor.last_accounts[$scope.tutor.last_accounts.length - 1].date_end
                moment(date_end).add(1, 'days').toDate()
            else
                new Date $scope.date_limit

        $scope.addAccountDialog = ->
            $scope.new_account_date_end = ''
            # @todo: узнать, как делается refresh
            $('#date-end').datepicker('destroy')
            $('#date-end').datepicker
                language	: 'ru'
                startDate   : getCalendarStartDate()
                autoclose	: true
                orientation	: 'bottom auto'

            $scope.dialog 'add-account'

        $scope.addAccount = ->
            Account.save
                date_end: convertDate($scope.new_account_date_end)
                tutor_id: $scope.tutor.id
            , (new_account) ->
                $scope.tutor.last_accounts.push(new_account)
                $scope.closeDialog 'add-account'





        ## Управление кареткой ##

        $scope.selectRow = (date) ->
            $('tr[class^=\'tr-\']').removeClass 'selected'
            $('.tr-' + date).addClass 'selected'
            return

        ###
        * Перевести курсор, если элемент существует
        ###

        moveCursor = (x, y, direction) ->
            # Определяем направление и изменяем координаты соответствующим образом
            switch direction
                when "left"     then x--
                when "right"    then x++
                when "up"       then y--
                when "down"     then y++

            # Если двигаемся в несуществующие поля
            return if x < 0 or y < 0

            # Получаем новый элемент
            el = $('#i-' + y + '-' + x)

            # Если элемент существует, двигаемся туда
            if el.length
                $scope.caret = 0
                el.focus()
            else
                moveCursor x, y, direction
                # Если не получилось, пытаемся передвинуться еще раз (перепрыгнуть через несколько ячеек сразу)
            return

        $scope.caret = 0 # Позиция каретки
        $scope.periodsCursor = (y, x, event) ->
            # console.log y, x, event, $("#" + y + "-" + x)
            # Получаем начальный элемент (с которого возможно сдвинемся)
            original_element = $("#i-#{y}-#{x}")
            console.log original_element, original_element.val()

            # Если был нажат 0, то подхватываем значение поля сверху
            if original_element.val() is "0" and original_element.val().length
                i = y - 1
                while i > 0
                    # Поверяем существует ли поле сверху
                    if $('#i-' + i + '-' + x).length and $('#i-' + i + '-' + x).val()
                        # Присваеваем текущему элементу значение сверху
                        original_element.val $('#i-' + i + '-' + x).val()
                        break
                    i--

            # Если внутри цифр, то не прыгаем к следующему элементу
            if original_element.caret() != $scope.caret
                $scope.caret = original_element.caret()
                return

            switch event.which
                # ВЛЕВО
                when 37 then moveCursor(x, y, "left")
                # ВВЕРХ
                when 38 then moveCursor(x, y, "up")
                # ВПРАВО
                when 39 then moveCursor(x, y, "right")
                # ВНИЗ
                when 13, 40 then moveCursor(x, y, "down")
