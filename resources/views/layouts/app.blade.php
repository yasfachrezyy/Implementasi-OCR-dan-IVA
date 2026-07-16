<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAJ Notary Hub</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 relative" x-data="{ sidebarOpen: false, currentView: 'dashboard' }">

    <div class="flex min-h-screen">
        
        <aside class="bg-red-900 text-white w-64 fixed inset-y-0 left-0 z-30 transform transition-transform duration-300 lg:translate-x-0 lg:fixed"
               :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
            
            @if (auth()->check())
                @if (auth()->user()->role == 'notaris')
                    @include('layouts.partials.sidebar-admin')
                @elseif (auth()->user()->role == 'staff')
                    @include('layouts.partials.sidebar-staff')
                @elseif (auth()->user()->role == 'klien')
                    @include('layouts.partials.sidebar-klien')
                @endif
            @endif
        </aside>
        
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black opacity-50 z-20 lg:hidden" x-cloak></div>

        <div class="lg:ml-64 flex-1 relative">
            
            <header class="flex items-center justify-between p-4 bg-white border-b lg:hidden">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('images/logo-notaris-merah.png') }}" alt="Logo" class="h-8 w-8">
                    <span class="font-bold text-gray-800">LAJ Notary Hub</span>
                </div>
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </header>

            <main class="p-6">
                <div x-show="currentView === 'dashboard'">
                    @yield('content')
                </div>

                <div x-show="currentView === 'iva'" x-cloak class="h-[calc(100vh-100px)]">
                    <iframe src="{{ route('iva.index') }}" class="w-full h-full border-none rounded-2xl shadow-lg"></iframe>
                </div>
            </main>
            
        </div>
    </div>

    @stack('scripts')
</body>
</html>