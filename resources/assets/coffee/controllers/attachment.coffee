angular
    .module 'Egerep'
    .factory 'Attachment', ($resource) ->
        $resource 'api/attachments/:id', {},
            update:
                method: 'PUT'
    .controller 'AttachmentsStats', ($scope, $rootScope, $http, $timeout, Months, UserService) ->
        $scope.getYears = ->
            count = 4
            i = 0
            years = []
            while i < count
                years.push moment().subtract('year', i).format('YYYY')
                i++
            years

        $scope.getUsersByYear = (year) ->
            _.chain($scope.data).where({year: parseInt(year)}).pluck('user_id').uniq().value()

        $scope.getDays = ->
            _.range(1, 32)

        $scope.dayExtremum = (day, year, val, mode) ->
            return false if not val

            condition = {year: parseInt(year)}
            condition.day = parseInt(day) if day isnt null

            data = _.where $scope.data, condition

            max = -999
            min = 999

            data.forEach (d) ->
                max = d.count if d.count > max
                min = d.count if d.count < min

            extremum = if mode is 'max' then max else min
            console.log(extremum, val, year) if day is null
            val == extremum

        $scope.totalExtremum = (year, val) ->
            return false if not val

            user_ids = $scope.getUsersByYear(year)
            return false if not user_ids.length

            max = -9999

            user_ids.forEach (user_id) ->
                v = $scope.getUserTotal(year, user_id)
                max = v if v > max

            val == max

        $scope.getUserTotal = (year, user_id) ->
            data = _.where $scope.data,
                year: parseInt(year)
                user_id: parseInt(user_id)
            sum = 0
            data.forEach (d) ->
                sum += d.count
            sum or ''

        $scope.getDayTotal = (year, day = null) ->
            condition = {year: parseInt(year)}
            condition.day = parseInt(day) if day isnt null
            data = _.where $scope.data, condition
            sum = 0
            data.forEach (d) ->
                sum += d.count
            sum or ''

        $scope.getValue = (day, year, user_id) ->
            d = _.find scope.data,
                day: parseInt(day)
                year: parseInt(year)
                user_id: parseInt(user_id)
            if d isnt undefined then d.count else ''

        $scope.$watch 'month', (newVal, oldVal) ->
            $rootScope.frontend_loading = true
            $http.post 'api/attachments/stats', {month: newVal}
            .then (response) ->
                $rootScope.frontend_loading = false
                $scope.data = response.data
        bindArguments($scope, arguments)
    .controller 'AttachmentsIndex', ($rootScope, $scope, $timeout, $http, AttachmentStates, AttachmentService, UserService, PhoneService, Subjects, Grades, Presence, YesNo, AttachmentVisibility, AttachmentErrors) ->
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true

        $scope.recalcAttachmentErrors = ->
            $scope.attachment_errors_updating = true
            $http.post 'api/command/model-errors', {model: 'attachments'}

        refreshCounts = ->
            $timeout ->
                $('.selectpicker option').each (index, el) ->
                    $(el).data 'subtext', $(el).attr 'data-subtext'
                    $(el).data 'content', $(el).attr 'data-content'
                $('.selectpicker').selectpicker 'refresh'

                $('.attachment-filters button').css 'background', 'none'
                $('.attachment-filters select > option[value!=""]:selected').parent('select').siblings('button').css('background', '#dceee5')
            , 100

        $scope.filter = ->
            $.cookie("attachments", JSON.stringify($scope.search), { expires: 365, path: '/' });
            $scope.current_page = 1
            $scope.pageChanged()

        $scope.changeState = (state_id) ->
            $rootScope.frontend_loading = true
            $scope.attachments = []
            $scope.current_page = 1
            loadAttachments($scope.current_page)
            window.history.pushState(state_id, '', 'attachments/' + state_id.toLowerCase());

        $timeout ->
            $scope.search = if $.cookie("attachments") then JSON.parse($.cookie("attachments")) else {}
            loadAttachments $scope.page
            $scope.current_page = $scope.page

        $scope.pageChanged = ->
            $rootScope.frontend_loading = true
            $rootScope.attachments = []
            loadAttachments $scope.current_page
            paginate('attachments', $scope.current_page)

        loadAttachments = (page) ->
            params = '?page=' + page

            $http.get "api/attachments#{ params }"
            .then (response) ->
                $scope.data = response.data.data
                $scope.attachments = response.data.data.data
                $scope.counts = response.data.counts
                $rootScope.frontend_loading = false
                refreshCounts()
    .controller 'AttachmentsNew', ($rootScope, $scope, $timeout, $http, AttachmentStates, AttachmentService, UserService, PhoneService, Subjects, Grades, Presence, YesNo, AttachmentVisibility, AttachmentErrors) ->
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true

        $scope.daysAgo = (date) ->
            now = moment(Date.now())
            date = moment(new Date(date).getTime())
            now.diff(date, 'days')

        $timeout ->
            load $scope.page
            $scope.current_page = $scope.page

        $scope.pageChanged = ->
            $rootScope.frontend_loading = true
            load $scope.current_page
            paginate('attachments/new', $scope.current_page)

        load = (page) ->
            params = '?page=' + page

            $http.get "api/attachments/new#{ params }"
            .then (response) ->
                console.log response
                $scope.counts = response.data.counts
                $scope.data = response.data
                $scope.attachments = response.data.data
                $rootScope.frontend_loading = false
