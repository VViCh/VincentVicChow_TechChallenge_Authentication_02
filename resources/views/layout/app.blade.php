<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication</title>
</head>
<body>
    <nav>
        @auth
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit">
                    Logout
                </button>
            </form>
        @else
            <a href="{{ route('login') }}">Login</a> |
            <a href="{{ route('register') }}">Register</a>
        @endauth
    </nav>

    <div class="container">
        @yield('content')
    </div>
</body>
</html>