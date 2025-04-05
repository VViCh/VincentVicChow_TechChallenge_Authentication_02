@extends('layout.app')

@section('content')
<h2>Reset Password</h2>

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <input type="email" name="email" placeholder="Email" required value="{{ old('email') }}">
    @error('email')
    <div>
        {{ $message }}
    </div>
    @enderror

    <input type="password" name="password" placeholder="New Password" required>
    @error('password')
    <div>
        {{ $message }}
    </div>
    @enderror

    <input type="password" name="password_confirmation" placeholder="Confirm Password" required>

    <button type="submit">Reset Password</button>
</form>
@endsection
