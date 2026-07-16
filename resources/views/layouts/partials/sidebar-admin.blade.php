<div class="flex flex-col h-full">
    <div class="p-5 border-b border-red-800">
        <div class="flex items-center space-x-3">
            <img src="{{ asset('images/Logo notaris bu lilis-putih.png') }}" alt="Logo Notaris" class="h-10 w-10 object-contain">
            <div>
                <h2 class="text-sm font-bold">Notaris & PPAT</h2>
                <p class="text-xs text-red-200">Lilis Aenun Jariah S.H., M.kn.</p>
            </div>
        </div>
    </div>

    <div class="p-5">
        <p class="text-sm font-medium truncate">
            Hi, {{ Auth::user()->name ?? 'Pengguna' }}!
        </p>
    </div>

    <nav class="flex-1 px-4 space-y-2">
        <a href="{{ route('notaris.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium text-red-100 hover:bg-red-800 hover:text-white rounded-md transition-colors duration-200">
            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h7.5"/></svg>
            Home
        </a>
        <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2 text-sm font-medium text-red-100 hover:bg-red-800 hover:text-white rounded-md transition-colors duration-200">
            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
            Profile
        </a>
        <a href="{{ route('notaris.permohonan.index') }}" class="flex items-center px-3 py-2 text-sm font-medium text-red-100 hover:bg-red-800 hover:text-white rounded-md transition-colors duration-200">
            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Daftar Permohonan
        </a>
        <a href="{{ route('notaris.staff.index') }}" class="flex items-center px-3 py-2 text-sm font-medium text-red-100 hover:bg-red-800 hover:text-white rounded-md">
            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-4.663M12 12.375a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z"/></svg>
            Kelola Staff
        </a>
        <a href="{{ route('notaris.klien.index') }}" class="flex items-center px-3 py-2 text-sm font-medium text-red-100 hover:bg-red-800 hover:text-white rounded-md">
            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zm10.293 8.293c-2.628.83-5.543.83-8.171 0m0 0c-2.628.83-5.543.83-8.171 0m8.171 0c-2.22 0-4.32-.424-6.233-1.218m12.466 0c-1.913.794-4.013 1.218-6.233 1.218"/></svg>
            Kelola Klien
        </a>
        <a href="{{ route('notaris.laporan.index') }}" class="flex items-center px-3 py-2 text-sm font-medium text-red-100 hover:bg-red-800 hover:text-white rounded-md transition-colors duration-200">
            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 100 15 7.5 7.5 0 000-15zM21 21l-5.197-5.197"/></svg>
            Laporan
        </a>
    </nav>
    
    <div class="p-4 mt-auto border-t border-red-800">
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <a href="{{ route('logout') }}"
            onclick="event.preventDefault(); this.closest('form').submit();"
            class="flex items-center px-3 py-2 text-sm font-medium text-red-100 hover:bg-red-800 hover:text-white rounded-md transition-colors duration-200">
                
                <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                </svg>
                
                Logout
            </a>
        </form>
    </div>
</div>