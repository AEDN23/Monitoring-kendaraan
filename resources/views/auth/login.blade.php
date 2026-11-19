@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <h5 class="mb-4 fw-bold text-dark">Masuk ke akun Anda</h5>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label small fw-semibold">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
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
                autocomplete="current-password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="••••••••"
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Remember me --}}
        <div class="mb-4 form-check">
            <input id="remember" name="remember" type="checkbox" class="form-check-input">
            <label for="remember" class="form-check-label small text-secondary">Ingat saya</label>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
            Masuk
        </button>
    </form>
@endsection
