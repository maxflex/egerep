angular.module 'Egerep'
    .service 'RecommendationService', (Recommendations, RecommendationTypes) ->
        this.getRecommendation = (tutor) ->
            month = moment().format 'M'
            # не 10 класс?
            if $scope.client.grade isnt 10
                # if
                console.log 1
