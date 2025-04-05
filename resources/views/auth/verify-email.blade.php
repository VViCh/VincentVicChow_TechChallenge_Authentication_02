@extends('layout.app')

@section('content')
<h1>Verify Your Email Address</h1>
<p>Before proceeding, please check your email for a verification link.</p>
<form method="POST" action="{{ route('verification.resend') }}">
    @csrf
    <button type="submit">Resend Verification Email</button>
</form>
@endsection