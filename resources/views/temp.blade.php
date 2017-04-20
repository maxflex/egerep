<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.4.0/css/bulma.css">
<style>
table {
  display: block;
}

thead {
  position: sticky;
  top: 0px;  /* trigger sticky when reaches coordonates */
}

thead, tbody, tfoot {
  display: table;
  width: 100%;
}
</style>
<body style='overflow: scroll'>
    <table class='table'>
        <thead>
            <tr>
                <td>
                    student/subject
                </td>
                @foreach(dateRange($year . '-09-01', ($year + 1) . '-06-30') as $date)
                <td style='white-space: nowrap'>
                    {{ date('y.m.d', strtotime($date)) }}
                </td>
                @endforeach
            </tr>
        </thead>
        @foreach($data as $d)
        <tr>
            <td>{{ $d->id_entity }}_{{ $d->id_subject }}</td>
            @foreach(dateRange($year . '-09-01', ($year + 1) . '-06-30') as $date)
            <td style='white-space: nowrap'>
                {{ in_array($date, $d->dates) ? '1' : '' }}
            </td>
            @endforeach
        </tr>
        @endforeach
    </table>
</body>
