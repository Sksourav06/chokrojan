<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    {{-- Dynamic title with a fallback --}}
    <title>@yield('title', 'Unique Service - Dashboard')</title>
    {{-- CSRF Token for security --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Fonts --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">

    {{-- Metronic Core CSS --}}
    <link href="{{ asset('plugins/global/plugins.bundle.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.bundle.css') }}" rel="stylesheet">

    {{-- Metronic Theme Specifics (Recommended to bundle these if possible, but kept as separate links here) --}}
    <link href="{{ asset('css/themes/layout/header/base/light.css') }}" rel="stylesheet">
    <link href="{{ asset('css/themes/layout/header/menu/light.css') }}" rel="stylesheet">
    <link href="{{ asset('css/themes/layout/aside/light.css') }}" rel="stylesheet">
    <link href="{{ asset('css/themes/layout/brand/light.css') }}" rel="stylesheet">

    {{-- External Icons: Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Custom Stylesheet --}}
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    {{-- Page specific styles (Styles pushed from child views using @push('styles')) --}}
    @stack('styles')
</head>

<body id="kt_body" class="header-fixed aside-enabled aside-fixed">

    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-row flex-column-fluid page">

            {{-- Sidebar/Aside Component --}}
            @include('layouts.aside')

            <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
                {{-- Header Component --}}
                @include('layouts.header')

                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    {{-- Main Content Injection Point --}}
                    @yield('content')
                </div>

                {{-- Footer Component --}}
                @include('layouts.footer')
            </div>
        </div>
    </div>

    {{-- Core JavaScript Libraries (Must load first) --}}
    <script src="{{ asset('plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('js/scripts.bundle.js') }}"></script>

    {{-- Page specific scripts (Injected here via @push('scripts') in child views) --}}
    @stack('scripts')


</body>

</html>