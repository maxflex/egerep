angular.module 'Egerep'
    .controller 'AddToList', ($scope, Genders, Grades, Subjects, TutorStates, Destinations) ->
        $scope.Genders = Genders
        $scope.Grades = Grades
        $scope.Subjects = Subjects
        $scope.TutorStates = TutorStates
        $scope.Destinations = Destinations
