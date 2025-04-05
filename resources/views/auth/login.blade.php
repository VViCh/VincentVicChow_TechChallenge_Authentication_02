@extends('layout.app')

@section('content')
<h2>Login</h2>

@if(session('status'))
    <div>{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf
    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
    @error('email')
    <div>
        {{ $message }}
    </div>
    @enderror

    <input type="password" name="password" placeholder="Password" required>
    @error('password')
    <div>
        {{ $message }}
    </div>
    @enderror

    <button type="submit">Login</button>
</form>

<p><a href="{{ route('password.request') }}">Forgot Password?</a></p>
<p>Don’t have an account? <a href="{{ route('register') }}">Register</a></p>
@endsection
