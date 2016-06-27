angular.module 'Egerep'
    .service 'RecommendationService', (Recommendations, RecommendationTypes) ->
        @get = (tutor) ->
            recommendation = @getRecommendation(tutor)
            recommendation.type_label = RecommendationTypes[recommendation.type]
            recommendation

        @getRecommendation = (tutor) ->
            month = moment().format 'M'
            # не 10 класс?
            if $scope.client.grade isnt 10
                if month >= 7 && month <= 10
                    if tutor.meeting_count >= 2
                        return Recommendations[1]
                    else
                        if tutor.meeting_count == 1
                            return Recommendations[2]
                        else
                            if tutor.active_clients_count >= 2
                                return Recommendations[3]
                            else
                                return Recommendations[4]
                else
                    if month >= 11 or month <= 2
                        if tutor.meeting_count >= 2
                            return Recommendations[5]
                        else
                            if tutor.meeting_count == 1
                                return Recommendations[6]
                            else
                                if tutor.active_clients_count >= 2
                                    return Recommendations[7]
                                else
                                    return Recommendations[8]
                    else
                        if tutor.meeting_count >= 2
                            return Recommendations[9]
                        else
                            return Recommendations[10]
            else
                if tutor.meeting_count >= 2
                    return Recommendations[11]
                else
                    if tutor.meeting_count == 1
                        return Recommendations[12]
                    else
                        if tutor.active_clients_count >= 2
                            return Recommendations[13]
                        else
                            return Recommendations[14]
        this
