@extends('layouts.app')

@section('title', 'Edit Kendaraan')
@section('header', 'Edit Kendaraan')

@section('content')
<div class="container-fluid p-0" style="max-width: 760px;">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold text-dark mb-0">Edit Kendaraan</h4>
            <div class="text-secondary small">Perbarui informasi {{ $vehicle->name }}</div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 p-4">
        <form action="{{ route('vehicles.update', $vehicle) }}" method="POST">
            @csrf
            @method('PUT')

            @include('vehicles._form')

            <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-4">
                    Simpan Perubahan
                </button>
                <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary px-3">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
