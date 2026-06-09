<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'WebCut Lite')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('head')
</head>
<body class="bg-[#FCFCFC] text-[#111111] font-sans h-screen flex flex-col overflow-hidden selection:bg-lime-300 selection:text-black">

@if(isset($hideSidebar))
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
        @yield('header')
        <main class="flex-1 overflow-y-auto overflow-x-hidden relative flex flex-col">
            @yield('content')
        </main>
    </div>
@else
    {{-- Global Top Navbar spans full width --}}
    @yield('header')

    {{-- Drawer sits below the navbar --}}
    <div class="drawer lg:drawer-open flex-1 h-full overflow-hidden relative">
        <input id="main-drawer" type="checkbox" class="drawer-toggle" />
        
        <div class="drawer-content flex flex-col h-full overflow-hidden relative">
            {{-- Main area Content --}}
            <main class="flex-1 overflow-y-auto overflow-x-hidden relative flex flex-col min-w-0">
                @yield('content')
            </main>
        </div>

        @include('partials.sidebar')
    </div>
@endif

    @stack('modals')

</body>
</html>
