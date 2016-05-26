@extends('app')
@section('title', 'Ошибка')
@section('controller', 'EmptyCtrl')

@section('content')
    <div class="aligner">
        <div class="aligner-item">
            <h5 class='center text-gray'>{{ $message }}</h5>
        </div>
    </div>
@endsection
