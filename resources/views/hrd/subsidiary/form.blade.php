@extends('layouts.app')

@section('title', $isEdit ? 'Edit Perusahaan' : 'Tambah Perusahaan')
@section('menuSubsidiaries', 'active')

@section('content')
    <div class="container mt-3">
        @component('components.card')
            @slot('header')
                {{ $isEdit ? 'Edit ' . $subsidiary->name : 'Tambah Data Perusahaan' }}
            @endslot
            <x-form.subsidiary :subsidiary="$subsidiary" :isEdit="$isEdit" />
        @endcomponent
    </div>
@endsection
