@extends('layouts.app')
@section('title', "Transfer Karyawan Antar Plant")
@section('menuSubsidiaries', 'active')
@section('content')

    <div class="container mt-3">
        @component('components.card')
            @slot('header')
                Transfer Karyawan Antar Plant
            @endslot
            <form action="{{ route('subsidiaries.transfer.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="from_subsidiary_id" class="form-label">Dari Plant</label>
                    <select name="from_subsidiary_id" id="from_subsidiary_id" class="form-select" required>
                        <option value="" disabled selected>Pilih Plant Asal</option>
                        @foreach ($subsidiaries as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                        @endforeach
                    </select>
                    @error('from_subsidiary_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="to_subsidiary_id" class="form-label">Ke Plant</label>
                    <select name="to_subsidiary_id" id="to_subsidiary_id" class="form-select" required>
                        <option value="" disabled selected>Pilih Plant Tujuan</option>
                        @foreach ($subsidiaries as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                        @endforeach
                    </select>
                    @error('to_subsidiary_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Transfer</button>
            </form>
        @endcomponent
    </div>
@endsection
