@extends('layouts.guest')

@section('title', 'Daftar')

@section('content')
    <h5 class="mb-4 fw-bold text-dark">Buat akun baru</h5>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Name --}}
        <div class="mb-3">
            <label for="name" class="form-label small fw-semibold">Nama Lengkap</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                class="form-control @error('name') is-invalid @enderror"
                placeholder="John Doe"
            >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label small fw-semibold">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="email"
                class="form-control @error('email') is-invalid @enderror"
                placeholder="nama@email.com"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label for="password" class="form-label small fw-semibold">Password</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="Minimal 8 karakter"
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="mb-4">
            <label for="password_confirmation" class="form-label small fw-semibold">Konfirmasi Password</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                class="form-control"
                placeholder="Ulangi password"
            >
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mb-3">
            Daftar Sekarang
        </button>
    </form>

    <div class="text-center small text-secondary">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Masuk di sini</a>
    </div>
@endsection
