<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Vehicle Monitor') }} – @yield('title', 'Masuk')</title>

    {{-- Offline Bootstrap 5 & Icons --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100 py-4">

    <div class="container" style="max-width: 440px;">
        {{-- Logo --}}
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-4 shadow-sm mb-3" style="width: 56px; height: 56px;">
                <i class="bi bi-speedometer2 fs-2"></i>
            </div>
            <h3 class="fw-bold text-dark mb-1">Vehicle Monitor</h3>
            <p class="text-secondary small mb-0">Pantau kendaraan Anda dengan mudah</p>
        </div>

        {{-- Card --}}
        <div class="card shadow-sm border-0 rounded-4 p-4 p-md-5">
            {{-- Flash errors --}}
            @if ($errors->any())
                <div class="alert alert-danger py-2 small mb-4" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success py-2 small mb-4" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
