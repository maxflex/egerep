angular.module('Egerep')
    .value 'AccountPeriods',
        0: 'initial'
        1: 'month'
        2: 'year'
        3: 'all'
    .value 'Destinations',
        r_k: 'репетитор едет к клиенту'
        k_r: 'клиент едет к репетитору'
    .value 'Workplaces',
        0: 'не работает в ЕГЭ-Центре'
        1: 'работает в ЕГЭ-Центре'

    .value 'Genders',
        male:   'мужской'
        female: 'женский'

    .value 'TutorStates',
        0: 'не установлено'
        1: 'на проверку'
        2: 'к закрытию'
        3: 'закрыто'
        4: 'к одобрению'
        5: 'одобрено'

    .value 'DebtTypes',
        0: 'не доплатил'
        1: 'переплатил'

    .value 'PaymentMethods',
        0: 'не установлено'
        1: 'стандартный расчет'
        2: 'яндекс.деньги'
        3: 'перевод на сотовый'
        4: 'перевод на карту'

    .value 'RequestStates',
        new:            'невыполненные'
        awaiting:       'в ожидании'
        finished:       'выполненные'
        deny:           'отказы'
        motivated_deny: 'мотивированный отказ'

    .value 'ArchiveStates',
        impossible: 'невозможно'
        possible: 'возможно'

    .value 'ReviewStates',
        unpublished: 'не опубликован'
        published:   'опубликован'

    .value 'AttachmentStates',
        new:        'новые'
        inprogress: 'рабочие'
        ended:      'завершенные'

    .value 'ReviewScores',
        1: 1
        2: 2
        3: 3
        4: 4
        5: 5
        6: 6
        7: 7
        8: 8
        9: 9
        10: 10
        11: 'отзыв не собирать'
#        11: 'не берет'
#        12: 'не помнит'
#        13: 'недоступен'
#        14: 'позвонить позже'

    .value 'Grades',
        1: '1 класс'
        2: '2 класс'
        3: '3 класс'
        4: '4 класс'
        5: '5 класс'
        6: '6 класс'
        7: '7 класс'
        8: '8 класс'
        9: '9 класс'
        10: '10 класс'
        11: '11 класс'
        12: 'студенты'
        13: 'остальные'

    .value 'Subjects',
        all:
            1: 'математика',
            2: 'физика',
            3: 'химия',
            4: 'биология',
            5: 'информатика'
            6: 'русский',
            7: 'литература',
            8: 'обществознание',
            9: 'история',
            10: 'английский',
            11: 'неизвестный предмет',
        ,
        full:
            1: 'Математика'
            2: 'Физика'
            3: 'Химия'
            4: 'Биология'
            5: 'Информатика'
            6: 'Русский язык'
            7: 'Литература'
            8: 'Обществознание'
            9: 'История'
            10: 'Английский язык'
        dative:
            1: 'математике'
            2: 'физике'
            3: 'химии'
            4: 'биологии'
            5: 'информатике'
            6: 'русскому языку'
            7: 'литературе'
            8: 'обществознанию'
            9: 'истории'
            10: 'английскому языку'
            11: 'неизвестному предмету'
        short: ['М', 'Ф', 'Р', 'Л', 'А', 'Ис', 'О', 'Х', 'Б', 'Ин'],
        three_letters:
            1: 'МАТ'
            2: 'ФИЗ'
            3: 'ХИМ'
            4: 'БИО'
            5: 'ИНФ'
            6: 'РУС'
            7: 'ЛИТ'
            8: 'ОБЩ'
            9: 'ИСТ'
            10: 'АНГ'
        short_eng:      ['math', 'phys', 'rus', 'lit', 'eng', 'his', 'soc', 'chem', 'bio', 'inf'],
    .value 'Branches',
        1:
            code:'TRG',
            full:'Тургеневская',
            short:'ТУР',
            address:'Мясницкая 40с1',
            color:'#FBAA33',
        2:
            code:'PVN',
            full:'Проспект Вернадского',
            short:'ВЕР',
            address:'',
            color:'#EF1E25',
        3:
            code:'BGT',
            full:'Багратионовская',
            short:'БАГ',
            address:'',
            color:'#019EE0',
        5:
            code:'IZM',
            full:'Измайловская',
            short:'ИЗМ',
            address:'',
            color:'#0252A2',
        6:
            code:'OPL',
            full:'Октябрьское поле',
            short:'ОКТ',
            address:'',
            color:'#B61D8E',
        7:
            code:'RPT',
            full:'Рязанский Проспект',
            short:'РЯЗ',
            address:'',
            color:'#B61D8E',
        8:
            code:'VKS',
            full:'Войковская',
            short:'ВОЙ',
            address:'',
            color:'#029A55',
        9:
            code:'ORH',
            full:'Орехово',
            short:'ОРЕ',
            address:'',
            color:'#029A55',
        11:
            code:'UJN',
            full:'Южная',
            short:'ЮЖН',
            address:'',
            color:'#ACADAF',
        12:
            code:'PER',
            full:'Перово',
            short:'ПЕР',
            address:'',
            color:'#FFD803',
        13:
            code:'KLG',
            full:'Калужская',
            short:'КЛЖ',
            address:'Научный проезд 8с1',
            color:'#C07911',
        14:
            code:'BRT',
            full:'Братиславская',
            short:'БРА',
            address:'',
            color:'#B1D332',
        15:
            code:'MLD',
            full:'Молодежная',
            short:'МОЛ',
            address:'',
            color:'#0252A2',
        16:
            code:'VLD',
            full:'Владыкино',
            short:'ВЛА',
            address:'',
            color:'#ACADAF',
