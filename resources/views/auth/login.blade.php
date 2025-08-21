@extends('layouts.web.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow p-4" style="width: 100%; max-width: 450px;">
        <h4 class="mb-4 text-center">Login</h4>

        {{-- ✅ Show any error messages --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form id="loginForm" method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                       name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            {{-- Password with eye toggle --}}
            <div class="mb-3 position-relative">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                           name="password" required>
                    <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                        <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                    </span>
                </div>
                @error('password')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            {{-- Remember Me --}}
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember Me</label>
            </div>

            {{-- Submit --}}
            <div class="d-flex justify-content-between align-items-center">
                <button id="loginBtn" type="submit" class="btn btn-primary">Login</button>
                @if (Route::has('password.request'))
                    <a class="text-decoration-none" href="{{ route('password.request') }}">
                        Forgot Your Password?
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Password Toggle + Enter Key Script --}}
<script>
    // Show/Hide password
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const passwordIcon = document.getElementById('togglePasswordIcon');
        passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordIcon.classList.toggle('bi-eye');
        passwordIcon.classList.toggle('bi-eye-slash');
    });

    // Pressing Enter anywhere triggers form submission
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevent accidental double-submit
            document.getElementById('loginBtn').click();
        }
    });
</script>
@endsection
