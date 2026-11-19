@extends('layouts.app')

@section('title', 'Tambah Jenis BBM')
@section('header', 'Tambah Jenis BBM')

@section('content')
<div class="container-fluid p-0" style="max-width: 540px;">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('fuel-types.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold text-dark mb-0">Tambah Jenis BBM</h4>
    </div>

    <div class="card border-0 shadow-sm rounded-3 p-4">
        <form action="{{ route('fuel-types.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label small fw-semibold">Nama Jenis BBM <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name"
                       value="{{ old('name') }}"
                       placeholder="Contoh: BP 92, Shell V-Power"
                       class="form-control @error('name') is-invalid @enderror"
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label small fw-semibold" for="is_active">Aktif (bisa dipilih saat input BBM)</label>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-4">
                    Simpan
                </button>
                <a href="{{ route('fuel-types.index') }}" class="btn btn-outline-secondary px-3">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
