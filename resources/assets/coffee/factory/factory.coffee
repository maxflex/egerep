angular.module('Egerep')
    .value 'AttachmentErrors',
        1: 'в стыковке не указан класс'
        2: 'в стыковке не указан предмет'
        3: 'не указаны условия стыковки'
        4: 'дата стыковки или архивации неверны'
        5: 'не указан комментарий к архивации'
        6: 'не указан прогноз'
        7: 'неверное сочетание прогноза, занятий в отчетности и занятий к проводке'
        8: 'стыковка скрыта, но остались занятия к проводке'
        9: 'не заполнен текст отзыва'
        10: 'не заполнена подпись к отзыву'
        11: 'отсутствует оценка в отзыве'
        12: 'дата архивации не совпадает с датой последнего занятия'
        13: 'стыковка должна быть скрыта'
        14: 'ошибка в соответствии правилам скрытых стыковок'

    .value 'LogTypes',
        create: 'создание'
        update: 'обновление'
        delete: 'удаление'
    .value 'Recommendations',
        1:
            text: 'У этого репетитора уже было несколько расчетов, поэтому ему можно доверить длительное обучение, требующееся данному клиенту'
            type: 0
        2:
            text: 'У этого репетитора был всего 1 расчет и ему можно доверить длительное обучение, но лучше поискать более проверенные варианты'
            type: 1
        3:
            text: 'С этим репетитором не было встреч и есть клиенты, за которых он еще не рассчитался. Отдавать этого клиента категорически нельзя'
            type: 2
        4:
            text: 'С этим репетитором не было встреч и у него нет активных клиентов. Отдавать ему клиента можно, но только в крайнем случае'
            type: 1
        5:
            text: 'У этого репетитора уже было несколько расчетов, поэтому ему можно доверить данного клиента'
            type: 0
        6:
            text: 'У этого репетитора был всего 1 расчет, то есть у него средний кредитный рейтинг. Если более проверенных репетиторов нет, ему можно доверить этого клиента'
            type: 1
        7:
            text: 'С этим репетитором не было встреч и есть клиенты, за которых он еще не рассчитался. Отдавать этого репетитора можно в самом крайнем случае'
            type: 2
        8:
            text: 'С этим репетитором не было встреч и у него нет активных клиентов. Риск сотрудничества средний, поэтому работать с репетитором можно, если нет других вариантов'
            type: 1
        9:
            text: 'У этого репетитора высокий кредитный рейтинг, но конец учебного года лучше использовать для проверки неизвестных репетиторов'
            type: 1
        10:
            text: 'Этому репетитору мы не доверяем, но сейчас отличное время для его проверки. Если сотрудничество будет успешным, то мы будем рекомендовать в следующем году как проверенного. Если он не заплатит, то невыплаты будут минимальными и репетитора мы закроем навсегда, в чем великая польза.'
            type: 0
        11:
            text: 'С 10-классниками нужно быть особенно аккуратными и этот репетитор в данном случае рекомендован'
            type: 0
        12:
            text: 'С этим репетитором была всего 1 встреча, поэтому давать его ученику 10 класса будет риском. Сделайте все, чтобы избежать этого, но если не получается – давать можно'
            type: 1
        13:
            text: 'С этим репетитором не было встреч и есть клиенты, за которых он еще не рассчитался. Нужно сделать все, чтобы 10-классник его не получил, так как 10 классы всегда продолжают заниматься и в 11 классе. Давать этого репетитора категорически нельзя'
            type: 2
        14:
            text: 'Этот репетитор для компании новый. Давать 10-класснику можно, но в самом крайнем случае'
            type: 2
    .value 'RecommendationTypes', ['очень рекомендован', 'средне рекомендован', 'не рекомендован']
    .value 'DebtTypes',
        0: 'не доплатил'
        1: 'переплатил'
    .value 'Weekdays',
        0: 'пн'
        1: 'вт'
        2: 'ср'
        3: 'чт'
        4: 'пт'
        5: 'сб'
        6: 'вс'
    .value 'Destinations',
        r_k: 'репетитор едет к клиенту'
        k_r: 'клиент едет к репетитору'
    .value 'Workplaces',
        0: 'не активен в системе ЕГЭ-Центре'
        1: 'активен в системе ЕГЭ-Центра'
        2: 'ведет занятия в ЕГЭ-Центре'
        3: 'ранее работал в ЕГЭ-Центре'

    .value 'Genders',
        male:   'мужской'
        female: 'женский'
    .value 'YesNo',
        0: 'нет'
        1: 'да'
    .value 'TutorStates',
        0: 'не установлено'
        1: 'на проверку'
        2: 'к закрытию'
        3: 'закрыто'
        4: 'к одобрению'
        5: 'одобрено'

    .value 'TutorPublishedStates',
        0: 'не опубликован'
        1: 'опубликован'

    .value 'PaymentMethods',
        0: 'не установлено'
        1: 'стандартный расчет'
        2: 'яндекс.деньги'
        3: 'перевод на сотовый'
        4: 'перевод на карту'

    .value 'ArchiveStates',
        impossible: 'невозможно'
        possible: 'возможно'

    .value 'ReviewStates',
        unpublished: 'не опубликован'
        published:   'опубликован'

    .value 'Existance', ['созданные', 'требующие создания']
    .value 'Presence', [
        ['есть', 'отсутствует'],
        ['есть', 'нет']
    ]

    .value 'AttachmentVisibility',
        0: 'показано'
        1: 'скрыто'

    .value 'AttachmentStates',
        new: 'новые'
        inprogress: 'рабочие'
        ended: 'завершенные'

    .value 'Checked', ['не проверено', 'проверено']

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
        12: 'отзыв собрать позже'

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
