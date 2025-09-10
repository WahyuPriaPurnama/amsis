@extends('layouts.app')
@section('title', 'List User')
@section('content')
    <div class="container mt-3">
        @component('components.card')
            @slot('header')
                Role Management
            @endslot
        @endcomponent
    </div>
@endsection
