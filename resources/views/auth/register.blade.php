@extends('layout.app')

@section('content')
<h2>Register</h2>

@if(session('status'))
<div>{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('auth.register') }}">
    @csrf
    <input type="text" name="name" placeholder="Name" value="{{ old('name') }}" required>
    @error('name')
    <div>
        {{ $message }}
    </div>
    @enderror

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

    <input type="password" name="password_confirmation" placeholder="Confirm Password" required>

    <button type="submit">Register</button>
</form>

<p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
@endsection
