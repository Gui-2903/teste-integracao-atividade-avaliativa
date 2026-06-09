<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <link href="{{ asset('css/materialize.min.css') }}" rel="stylesheet">
    </head>
    <body class="">
        <header>
            <nav>
                <div class="nav-wrapper container">
                    <a href="/" class="brand-logo">{{ config('app.name', 'App') }}</a>
                    <ul id="nav-mobile" class="right hide-on-med-and-down">
                        @if (Route::has('login'))
                            @auth
                                <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                            @else
                                <li><a href="{{ route('login') }}">Login</a></li>
                                @if (Route::has('register'))
                                    <li><a href="{{ route('register') }}">Register</a></li>
                                @endif
                            @endauth
                        @endif
                    </ul>
                </div>
            </nav>
        </header>

        <main class="container" style="margin-top:2rem">
            @yield('content')
        </main>

        <footer class="page-footer">
            <div class="container">&copy; {{ date('Y') }} {{ config('app.name') }}</div>
        </footer>

        {{-- Materialize JS local file --}}
        <script src="{{ asset('js/materialize.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof M !== 'undefined' && M.AutoInit) {
                    M.AutoInit();
                }
            });
        </script>
    </body>
</html>
