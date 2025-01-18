<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel App</title>
    @vite('resources/css/app.css')
</head>
<body>
    <header>
        <nav>
        <h1>
            <a href="{{ route('home') }}">Laravel App</a>
        </h1>

        @guest
            <a href="{{ route('show.login') }}" class="btn">Login</a>
        @endguest

        @auth
            <span class="border-r-2 pr-5">
            Hi there, {{ Auth::user()->name }}!
            </span>
            <a href="{{ route('show.account') }}" class="btn">Account</a>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn">Logout</button>
            </form>
        @endauth
        </nav>
    </header>

    <main class="container">
        {{ $slot }}
    </main>
</body>
</html>