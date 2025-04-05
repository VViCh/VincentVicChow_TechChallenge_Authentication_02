@extends('layout.app')

@section('content')
<h2>Enter OTP</h2>

@if(session('status'))
    <div>{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('auth.otp.verify', ['user' => request()->user]) }}">
    @csrf
    <input type="text" name="otp" placeholder="Enter OTP" required>
    @error('otp')
    <div>
        {{ $message }}
    </div>
    @enderror

    <button type="submit">Verify</button>
</form>
@endsection
