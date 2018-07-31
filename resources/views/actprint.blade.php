<div style='page-break-after: always'>
    <div style="margin-left:2cm;">
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,400i,500,500i,700,700i,900,900i&amp;subset=cyrillic,cyrillic-ext" rel="stylesheet">
        <style>
            body {
                font-family: 'Roboto', system-ui, sans-serif;
                font-size: 13px;
            }
            p {
                text-align: justify !important;
                text-indent: 1cm;
            }
            p.no-ident {
                text-indent: initial;
            }
        </style>
        <div style="text-align: right">
            <div>Приложение № 1</div>
            <div>к Договору оказания платных</div>
            <div>образовательных услуг</div>
            <div>№{{ $d->id_contract }} от {{ dateFormat2($d->first->date) }}</div>
        </div>

        <h4 style="text-align:center;margin-bottom: 0; font-weight: 400">АКТ ОБ ОКАЗАННЫХ УСЛУГАХ</h4>
        <h4 style="text-align:center;margin-top: 0; font-weight: 400">ПО ДОГОВОРУ №{{ $d->id_contract }} от {{ dateFormat2($d->first->date) }}</h4>

        <div style="display: inline-block; width: 100%; margin-bottom: 20px">
            <div>г. Москва «27» июня 2018г.</div>
            <div>Мы, нижеподписавшиеся:</div>
        </div>

        <p>
            От имени Заказчика: {{ $d->r->last_name}} {{ $d->r->first_name }} {{ $d->r->middle_name }},
            и от имени Исполнителя Генеральный директор ООО «ЕГЭ-ЦЕНТР» Эрдман Константин Александрович,
            действующий на основании Устава, составили акт о том,
            что в соответствии с обязательствами, предусмотренными
            Договором от {{ dateFormat2($d->first->date) }} №{{ $d->id_contract }}
            Исполнитель оказал Заказчику в полном объеме услуги
            на сумму: {{ $d->sum }} ({{ num2str($d->sum) }}).
            НДС не облагается. Заказчик претензий к Исполнителю не имеет.
        </p>

        <p class="no-ident">Настоящий акт составлен в двух экземплярах, по одному для каждой Стороны.</p>

        <div style='margin: 60px 0'>
            <div style='display: inline-block; width: 50%'>
                Заказчик
            </div>
            <div style='display: inline-block; width: 50%'>
                <div style='margin-bottom: 20px'>
                    Исполнитель
                </div>
                <div>
                    Генеральный директор ООО «ЕГЭ- Центр»
                </div>
            </div>
        </div>


        <div>
            <div style='display: inline-block; width: 50%'>
                ________________/________________
            </div>
            <div style='display: inline-block; width: 50%'>
                ________________/ Эрдман. К. А.
            </div>
        </div>

    </div>
</div>
