angular
    .module 'Egerep'



    #
    #   LIST CONTROLLER
    #
    .controller "ClientsIndex", ($scope, $timeout, Client) ->
        $scope.clients = Client.query()



    #
    #   ADD/EDIT CONTROLLER
    #
    .controller "ClientsForm", ($scope, $timeout, $interval, $http, $q, Client, User, RequestStatus, Subjects) ->
        $scope.RequestStatus = RequestStatus
        $scope.Subjects = Subjects
        $scope.dataLoaded = $q.defer()

        # Save everything
        $scope.edit = ->
            $scope.ajaxStart()
            $scope.client.$update()
                .then (response) ->
                    $scope.ajaxEnd()


        # get teacher
        $timeout ->
            $scope.users = User.query()

            $http.get 'api/tutors/list'
                .success (tutors) ->
                    $scope.tutors = tutors

            if $scope.id > 0
                $scope.client = Client.get {id: $scope.id}, (client) ->
                    $scope.dataLoaded.resolve(client)
                    $scope.selected_request = if $scope.request_id then _.findWhere(client.requests, {id: $scope.request_id}) else client.requests[0]
                    # set the default list
                    if client.subject_list isnt null
                        $scope.selected_list_id = client.subject_list[0]
                        # set the default attachment, if any
                        if client.attachments[$scope.selected_list_id]
                            $scope.selected_attachment = client.attachments[$scope.selected_list_id][0]
                    rebindMasks()

        $scope.attachmentExists = (subject_id, tutor_id) ->
            return false if $scope.client.attachments[subject_id] is undefined
            _.findWhere($scope.client.attachments[subject_id], {tutor_id: tutor_id}) isnt undefined

        $scope.selectAttachment = (tutor_id) ->
            $scope.selected_attachment = _.findWhere $scope.client.attachments[$scope.selected_list_id],
                tutor_id: tutor_id

        $scope.setList = (subject_id) ->
            console.log subject_id
            $scope.selected_list_id = subject_id

        $scope.selectRequest = (request) ->
            $scope.selected_request = request

        $scope.toggleUser = ->
            new_user = _.find $scope.users, (user) ->
                user.id > $scope.selected_request.user.id
            # if toggeled to the last user, start the loop over
            new_user = $scope.users[0] if new_user is undefined
            $scope.selected_request.user = new_user

        $scope.getUser = (user_id) ->
            _.findWhere $scope.users,
                id: user_id

        $scope.addListSubject = ->
            $scope.client.subject_list.push($scope.list_subject_id)
            $scope.client.lists[$scope.list_subject_id] = []
            $scope.selected_list_id = $scope.list_subject_id

            delete $scope.list_subject_id
            $('#add-subject').modal 'hide'

        $scope.addListTutor = ->
            $scope.client.lists[$scope.selected_list_id].push $scope.list_tutor_id
            delete $scope.list_tutor_id
            $('#add-tutor').modal 'hide'

        $scope.newAttachment = (tutor_id, subject_id) ->
            $scope.client.attachments[subject_id] = [] if not $scope.client.attachments[subject_id]

            new_attachment =
                tutor_id: tutor_id
                client_id: $scope.id

            $scope.client.attachments[subject_id].push new_attachment
            $scope.selected_attachment = new_attachment

        $scope.addRequest = ->
            new_request =
                id: null
                client_id: $scope.id
                status: 'new'

            $scope.client.requests.push(new_request)
            $scope.selected_request = new_request


        # parse textarea for tutor IDS
        $scope.$watch 'selected_request.comment', (newVal, oldVal) ->
            return if newVal is undefined and oldVal is undefined
            newVal = oldVal if newVal is undefined
            $scope.tutor_ids = []
            matches = newVal.match /Репетитор [\d]+/gi
            $.each matches, (index, match) ->
                tutor_id = match.match /[\d]+/gi
                $scope.tutor_ids.push parseInt(tutor_id)

        # refresh selectpicker on $selected_attachment update
        $scope.$watch 'selected_attachment', (newVal, oldVal) ->
            return if newVal is undefined
            sp 'attachment-subjects', 'предмет' if oldVal is undefined
            spRefresh 'attachment-subjects' if oldVal isnt undefined
