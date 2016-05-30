angular.module 'Egerep'
    .service 'TutorService', ($http) ->
        this.translit =
            'А':'A'
            'Б':'B'
            'В':'V'
            'Г':'G'
            'Д':'D'
            'Е':'E'
            'Ё':'E'
            'Ж':'Gh'
            'З':'Z'
            'И':'I'
            'Й':'Y'
            'К':'K'
            'Л':'L'
            'М':'M'
            'Н':'N'
            'О':'O'
            'П':'P'
            'Р':'R'
            'С':'S'
            'Т':'T'
            'У':'U'
            'Ф':'F'
            'Х':'H'
            'Ц':'C'
            'Ч':'Ch'
            'Ш':'Sh'
            'Щ':'Sch'
            'Ъ':'Y'
            'Ы':'Y'
            'Ь':'Y'
            'Э':'E'
            'Ю':'Yu'
            'Я':'Ya'
            'а':'a'
            'б':'b'
            'в':'v'
            'г':'g'
            'д':'d'
            'е':'e'
            'ё':'e'
            'ж':'gh'
            'з':'z'
            'и':'i'
            'й':'y'
            'к':'k'
            'л':'l'
            'м':'m'
            'н':'n'
            'о':'o'
            'п':'p'
            'р':'r'
            'с':'s'
            'т':'t'
            'у':'u'
            'ф':'f'
            'х':'h'
            'ц':'c'
            'ч':'ch'
            'ш':'sh'
            'щ':'sch'
            'ъ':'y'
            'ы':'y'
            'ь':'y'
            'э':'e'
            'ю':'yu'
            'я':'ya'

        this.default_tutor =
            gender: "male"
            branches: []
            phones:   []
            subjects: []
            grades:   []
            svg_map:  []
            markers:  []
            state:       0
            in_egecentr: 0

        this.getFiltered = (search_data) ->
            $http.post 'api/tutors/filtered', search_data

        this.getDebtMap = (search_data) ->
            $http.post 'api/debt/map', search_data

        this.generateLogin = (tutor) ->
            login = ''
            login += this.translit[letter] for letter in tutor.last_name.toLowerCase()
            login = login.slice 0, 3
            login += '_' + this.translit[tutor.first_name.toLowerCase()[0]] + this.translit[tutor.middle_name.toLowerCase()[0]]
            login

        this.generatePassword = () ->
            Math.floor(10000000+Math.random()*89999999)
        this
