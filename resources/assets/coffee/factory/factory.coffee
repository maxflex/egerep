angular.module('Egerep')
    .value 'Approved',
        0: 'не подтвержден'
        1: 'подтвержден'
    .value 'Confirmed',  # есть и approved, но там текст немножко другой.
        0: 'подтвердить'
        1: 'подтверждено'
    .value 'Months',
        1: 'январь'
        2: 'февраль'
        3: 'март'
        4: 'апрель'
        5: 'май'
        6: 'июнь'
        7: 'июль'
        8: 'август'
        9: 'сентябрь'
        10: 'октябрь'
        11: 'ноябрь'
        12: 'декабрь'

    .value 'Years',
        2017: '2017–2018 уч. г.'
        2018: '2018–2019 уч. г.'

    .value 'Notify', ['напомнить', 'не напоминать']

    .value 'AttachmentErrors',
        1: 'в стыковке не указан класс'
        2: 'в стыковке не указан хотя бы 1 предмет'
        3: 'в стыковке поле условия пусто'
        4: 'стыковка скрыта и занятий к проводке > 0'
        5: 'стыковка скрыта и архивация отсутствует'
        6: 'есть занятия до даты стыковки'
        7: 'есть занятия после даты архивации'
        8: 'есть занятия после последнего расчета'
        9: 'дата архивации раньше даты стыковки'
        10: 'поле детали архивации пусто'
        11: 'если занятий не было, измените дату архивации (через 7 дней после даты стыковки)'
        12: 'если занятий не было, скройте стыковку'
        13: 'занятия и прогноз не сочетаются'
        14: 'занятия и прогноз не сочетаются'
        15: 'если занятия к проводке не установлены, дата архивации должна совпадать с датой последнего занятия'
        16: 'если занятия к проводке не установлены, скройте стыковку'
        17: 'класс клиента  и статус разархивации не сочетаются'
        18: 'некорректный прогноз'
        19: 'некорректное количество занятий к проводке'
        20: 'некорректная сумма за занятие в отчетности'
        21: 'некорректная комиссия за занятие в отчетности'

    .value 'ReviewErrors',
        1: 'отзыв опубликован + нет отзыва'
        2: 'отзыв опубликован + нет подписи'
        3: 'отзыв опубликован + оценка НЕ = от 1 до 10'
        4: 'оценка с 1 по 10 + текст отзыва пусто'
        5: 'текст отзыва НЕ пусто + оценка пусто'
        6: 'оценка = от 6 до 10 + отзыв не опубликован'

    .value 'TutorErrors',
        1: 'в анкете нет ни одного телефона'
        2: 'дублирование телефонного номера в нескольких анкетах'
        3: 'статус репетитора: опубликован, к одобрению, одобрено, однако не заполнено одно из полей, использующееся на сайте'
        4: 'статус репетитора опубликован + у репетитора отустствует фото'

    .value 'UnderModer',
        0: 'на рассмотрении'
        1: 'одобрено'
        2: 'отказано'

    .value 'RequestErrors',
        1: 'статус заявки выполнено + в заявке нет ни одной стыковки'
        2: 'статус заявки отказ + в заявке есть стыковки'
        3: 'статус заявки отказ + ответственный не установлен'

    .value 'AccountErrors',
        1: 'в расчете отсутствуют платежи (в том числе взаимозачеты)'
        2: 'в расчете не проведено ни одного занятия'

    .value 'LogTypes',
        create: 'создание'
        update: 'обновление'
        delete: 'удаление'
        authorization: 'авторизация'
        url: 'просмотр URL'
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
        0: 'не активен в системе'
        1: 'преподаватели запаса'
        2: 'ведут занятия сейчас'
        3: 'ранее работал'
        4: 'готов к собеседованию'

    .value 'Genders',
        male:   'мужской'
        female: 'женский'
    .value 'YesNo',
        0: 'нет'
        1: 'да'

    .value 'TutorPublishedStates',
        0: 'не опубликован'
        1: 'опубликован'

    .value 'PaymentMethods',
        0: 'стандартный расчет'
        1: 'яндекс.деньги'
        2: 'перевод на карту'
        3: 'онлайн платеж'

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

    .value 'AttachmentState',
        new: 'новый'
        inprogress: 'рабочий'
        ended: 'завершенный'

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
    
    .value 'TutorSources',
        0: 'ЕГЭ-Репетитор'
        1: 'ЕГЭ-Центр'
        2: 'HH.ru'

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
        12: 'студенты вуза'
        14: 'студенты колледжа'
        13: 'остальные'

    .value 'CallStatuses',
        1: 'входящие без ответа'
        2: 'соответствие договорам'
        3: 'нет повторов в рамках 2 недель'
        4: 'преподаватель'

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
            11: 'география',
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
            11: 'География'
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
            11: 'географии'
        short: ['М', 'Ф', 'Р', 'Л', 'А', 'Ис', 'О', 'Х', 'Б', 'Ин', 'Г'],
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
            11: 'ГЕО'
        short_eng: ['math', 'phys', 'rus', 'lit', 'eng', 'his', 'soc', 'chem', 'bio', 'inf', 'geo'],
