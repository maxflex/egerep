@extends('app')
@section('title', 'Стыковки')
@section('controller', 'AttachmentsErrors')

@section('content')
    <table class="table">
        <thead class="bold small">
            <tr>
                <td width='150'></td>
                <td>код ошибки</td>
            </tr>
        </thead>
        <tbody>
            @foreach($errors as $error)
                <tr>
                    <td>
                        <a href="{{ $error->link }}" target="_blank">стыковка {{ $error->id }}</a>
                    </td>
                    <td>
                        @foreach($error->codes as $index => $code)
                            <span ng-attr-aria-label="{{ AttachmentErrors[<?= $index ?>] }}" class='hint--bottom-right'>{{ $code }}</span>{{ ($index + 1 < count($error->codes)) ? ', ' : ''}}
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
