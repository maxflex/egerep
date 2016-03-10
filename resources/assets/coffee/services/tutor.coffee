angular.module 'Egerep'
    .service 'TutorService', ($http) ->
        this.getFiltered = (search_data) ->
            $http.post 'api/tutors/filtered', search_data

        this
