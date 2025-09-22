<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="rtl">

<head>
    <!-- Head -->
    @include('partials.dashboard._head')

    <!-- CSS -->
    @stack('css')
</head>

<body class="vertical-layout vertical-menu-modern 2-columns   menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu-modern" data-col="2-columns">

    <!-- Header -->
    @include('partials.dashboard._header')
    <!-- Sidebar -->
    @include('partials.dashboard._sidebar')
    <!-- Content -->
    @yield('content')
    <!-- Footer -->
    @include('partials.dashboard._footer')
    <!-- Scripts -->
    @include('partials.dashboard._scripts')

    <!-- JS -->
    @stack('js')
</body>

</html>
