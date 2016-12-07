
angular
  .module 'Egerep'
  .controller 'SearchCtrl', ($scope, $http) ->
    console.log 'test2r'

    viewVue = new Vue({
      el: '#searchResult',
      data: {
        lists:[],
        results: -1,
        active: 0
      }
    })

    $scope.result = [];

    active = 0;

    $scope.links = {}; #храним ссылки
    $scope.oldQuery = ''; #храним предидущие значение для поиска
    $scope.query = ''; #поисковый запрос

    scroll = () ->
      totalObject = Object.keys $scope.links
        .length
      $ '#searchResult'
        .scrollTop((viewVue.active - 4) * 30)

    $scope.stoper = ($event) ->
      if $event.keyCode == 38 || $event.keyCode == 40
        $event.preventDefault()

    $scope.key = ($event) ->
      if $scope.query == ''
        console.log 'clear'
        viewVue.lists = []
        viewVue.active = 0;
        viewVue.results = -1;

      if $event.keyCode == 38 #клавиша стрелка вверх
        if angular.isUndefined $scope.success.data #проверка на наличе данных
            return false

        if viewVue.active > 0
          viewVue.active--;

        build()
        scroll()
        $event.preventDefault()
      else if $event.keyCode == 40 #клавиша стрелка вниз
        if angular.isUndefined $scope.success.data #проверка на наличе данных
          return false
        if viewVue.active < $scope.success.data.results
          viewVue.active++;

        build()

        if viewVue.active > 4
          scroll()
      else if $event.keyCode == 13  #ентер
        if !angular.isUndefined $scope.links[viewVue.active]
          window.open $scope.links[viewVue.active]
      else
        if $scope.oldQuery != $scope.query
          if !angular.isUndefined($scope.query) && $scope.query != ''
            $http.post '/api/search', {query: $scope.query}
              .then (success) ->
                if success.data.results == 0
                  viewVue.lists = []
                  viewVue.active = 0;
                  viewVue.results = 0;
                  height = $('#searchResult').height();
                  $('#searchResult .notFound')
                    .css('height', height-10)
                    .css('padding-top', parseInt(height/2) - 20)
                  height = null

                  $scope.success = {}; # обнуляем результат поиска
                else
                  viewVue.active = 0;
                  viewVue.lists = []
                  $scope.success = success;
                  all = 0
                  if $scope.success.data.clients.length > 0
                    angular.forEach $scope.success.data.clients, (row) ->
                      row.type = 'clients'
                      all++
                      $scope.links[all] = 'client/' + row.id
                      viewVue.lists.push(row)

                  if $scope.success.data.teachers.length > 0
                    angular.forEach $scope.success.data.teachers, (row) ->
                      row.type = 'teachers'
                      all++
                      $scope.links[all] = 'tutors/' + row.id + '/edit'
                      viewVue.lists.push(row)

                  viewVue.results = $scope.success.data.results;
                  console.log success.data
                  build()
              ,(error) ->
                console.log 'error'
                viewVue.lists = []
                viewVue.active = 0;
                viewVue.results = 0;
          else
            $scope.success = {}; # обнуляем результат поиска
            #angular.element("#searchResult").html('');
            viewVue.lists = []
            viewVue.active = 0;
            viewVue.results = -1;
          $scope.oldQuery = $scope.query
      false

    build = ->
      #console.log 'build in progress3'




#навешиваем событие по моменту зазагрузки
$(document).ready ->
  #вешеаем событие по клику по кнопке
  $('#searchModalOpen').click ->
    windowHeight = window.innerHeight; # определеяем высоту выдимой облости
    windowWidth = window.innerWidth; # определеяем ширину выдимой облости
    topPadding = parseInt windowHeight/4  #определяем отступ справа для позиционировния окна
    leftPadding = parseInt windowWidth/4  #определяем отступ cлева для позиционировния окна
    windowHeigh50 = parseInt windowHeight/2 #определяем высоту окна
    windowWidth50 = parseInt windowWidth/2  #определяем ширину окна
    modalContent = $ '#searchModal .modal-content' #задем ширину и высоту окна
    modalContent.css 'height', windowHeigh50
    modalContent.css 'width',windowWidth50
    modalDialog = $ '#searchModal .modal-dialog' #задаем отступ у окна
    modalDialog.css 'margin-top', topPadding
    modalDialog.css 'margin-left',leftPadding
    $('#searchResult').css 'height', windowHeigh50 - 70
    $('#searchModal').modal({keyboard: true})
    delayFunction = ()-> $('#searchQueryInput').focus()
    setTimeout delayFunction, 500
    false
  null