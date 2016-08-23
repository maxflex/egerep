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
