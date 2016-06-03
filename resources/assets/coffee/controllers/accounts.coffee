angular.module('Egerep')
    .controller 'AccountsHiddenCtrl', ($scope, Grades, Attachment) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            bindDraggable()

        # draggable
        bindDraggable = ->
            $(".client-draggable").draggable
                helper: 'clone'
                revert: 'invalid'
                appendTo: 'body'
                activeClass: 'drag-active'
                start: (event, ui) ->
                    $(this).css "visibility", "hidden"
                stop: (event, ui) ->
                    $(this).css "visibility", "visible"

            $(".client-droppable").droppable
                tolerance: 'pointer'
                hoverClass: 'client-droppable-hover'
                drop: (event, ui) ->
                    # ui.draggable.remove()
                    client_id      = $(ui.draggable).data('id')
                    client         = $scope.findById($scope.clients, client_id)
                    $scope.clients = removeById($scope.clients, client_id)

                    Attachment.update
                        id: client.attachment_id
                        hide: 0

                    $scope.visible_clients_count++
                    $scope.$apply()
    .controller 'AccountsCtrl', ($rootScope, $scope, $http, $timeout, Account, PaymentMethods, Grades, Attachment, Weekdays, AttachmentStates, PhoneService, AttachmentVisibility, DebtTypes) ->
        bindArguments($scope, arguments)
        $scope.current_scope  = $scope
        $scope.current_period = 0
        $scope.all_displayed  = false

        angular.element(document).ready ->
            $scope.loadPage()

        $scope.loadPage = (type) ->
            $rootScope.frontend_loading = true
            $http.get "api/accounts/#{$scope.tutor_id}" + (if $scope.current_period then "?date_limit=#{$scope.date_limit}" else "")
            .success (response) ->
                    renderData(response)
                    $scope.current_period++

        renderData = (data) ->
            # если у нового tutor last_accounts=null, то загрузили всё
            if data.account is null
                $scope.date_limit = $scope.first_attachment_date
                $scope.all_displayed = true
            else
                if not $scope.current_period
                    $scope.tutor = data
                else
                    $scope.tutor.last_accounts.unshift(data.account)
                    $scope.date_limit = moment(data.account.date_end).subtract(7, 'days').format('YYYY-MM-DD')
                    $scope.left       = data.left
            $rootScope.frontend_loading = false
            $('.accounts-table').stickyTableHeaders('destroy')
            $timeout ->
                $('.accounts-table').stickyTableHeaders()
                bindDraggable()
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

        $scope.getDay = (date) ->
            moment(date).day()

        $scope.accountInfo = (client) ->
            $scope.popup_attachment = null
            $('#account-info').modal 'show'

            $scope.selected_client = client

            Attachment.get {id: client.attachment_id}, (response) ->
                $scope.popup_attachment = response

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
                current_date = $scope.date_limit
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

        getCommission = (val) ->
            if val.indexOf('/') isnt -1
                val = val.split('/')[1]
                return if val then parseInt(val) else 0
            else
                return Math.round(parseInt(val) * .25)

        # всего занятий
        $scope.totalLessons = (account, client_id) ->
            lessons_count = 0
            $.each $scope.tutor.last_accounts, (index, account) ->
                lessons_count += $scope.periodLessons(account, client_id)
            lessons_count || null

        # всего занятий в периоде
        $scope.periodLessons = (account, client_id) ->
            return null if not account.data[client_id]
            lessons_count = 0
            $.each account.data[client_id], (index, value) ->
                lessons_count++ if value
            lessons_count || null
            # if account.data[client_id] then Object.keys(account.data[client_id]).length else 0

        $scope.totalCommission = (account) ->
            total_commission = 0
            $.each account.data, (index, account_data) ->
                $.each account_data, (index, val) ->
                    total_commission += getCommission(val) if val isnt ''
            total_commission



        ## Управление кареткой ##

        $scope.selectRow = (date) ->
            $('.tr-' + date).addClass 'selected'
            return
        $scope.deselectRow = (date) ->
            $('.tr-' + date).removeClass 'selected'
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


        # draggable
        bindDraggable = ->
            $(".client-draggable").draggable
                helper: 'clone'
                revert: 'invalid'
                appendTo: 'body'
                activeClass: 'drag-active'
                start: (event, ui) ->
                    $(this).css "visibility", "hidden"
                stop: (event, ui) ->
                    $(this).css "visibility", "visible"

            $(".client-droppable").droppable
                tolerance: 'pointer'
                hoverClass: 'client-droppable-hover'
                drop: (event, ui) ->
                    # ui.draggable.remove()
                    client_id      = $(ui.draggable).data('id')
                    client         = $scope.findById($scope.clients, client_id)
                    $scope.clients = removeById($scope.clients, client_id)

                    Attachment.update
                        id: client.attachment_id
                        hide: 1

                    $scope.hidden_clients_count++
                    $scope.$apply()
