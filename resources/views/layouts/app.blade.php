<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Bank App') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="app-body">
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-dark app-navbar">
            <div class="container">
                <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ url('/') }}">
                    <i class="fas fa-piggy-bank fa-lg"></i>
                    {{ config('app.name', 'Bank App') }}
                </a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto gap-1">
                        <li class="nav-item">
                            <a class="nav-link px-3 rounded" href="{{ route('transactions.index') }}">
                                <i class="fas fa-list me-1"></i> Transactions
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle px-3 rounded" href="#" id="transactionsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-chart-line me-1"></i> Reports
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="transactionsDropdown">
                                <li><a class="dropdown-item" href="{{ route('transactions.monthly-summary') }}"><i class="fas fa-calendar-alt me-2"></i>Monthly Summary</a></li>
                                <li><a class="dropdown-item" href="{{ route('transactions.year-summary') }}"><i class="fas fa-chart-bar me-2"></i>Year Summary</a></li>
                                <li><a class="dropdown-item" href="{{ route('transactions.counterparty') }}"><i class="fas fa-users me-2"></i>Counterparties</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle px-3 rounded" href="#" id="managementDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-cog me-1"></i> Management
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="managementDropdown">
                                <li><a class="dropdown-item" href="{{ route('transactions.import') }}"><i class="fas fa-file-import me-2"></i>Import Transactions</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('categories.index') }}"><i class="fas fa-tags me-2"></i>Categories</a></li>
                                <li><a class="dropdown-item" href="{{ route('mapping-profiles.index') }}"><i class="fas fa-map me-2"></i>Mapping Profiles</a></li>
                            </ul>
                        </li>
                    </ul>

                    <ul class="navbar-nav ms-auto gap-1">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link px-3 rounded" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link px-3 rounded" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle px-3 rounded d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" v-pre>
                                    <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>{{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="app-main">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html> 