angular.module 'Egerep'
    .directive 'plural', ->
        restrict: 'E'
        scope:
            count: '='      # кол-во
            type: '@'       # тип plural age | student | ...
            noneText: '@'   # текст, если кол-во равно нулю
        templateUrl: 'directives/plural'
        controller: ($scope, $element, $attrs, $timeout) ->
            $scope.textOnly = $attrs.hasOwnProperty('textOnly')

            $scope.when =
                'age': ['год', 'года', 'лет']
                'student': ['ученик', 'ученика', 'учеников']
                'minute': ['минуту', 'минуты', 'минут']
                'meeting': ['встреча', 'встречи', 'встреч']
                'score': ['балл', 'балла', 'баллов']
                'rubbles': ['рубль', 'рубля', 'рублей']
                'lesson': ['занятие', 'занятия', 'занятий']
                'client': ['клиент', 'клиента', 'клиентов']
