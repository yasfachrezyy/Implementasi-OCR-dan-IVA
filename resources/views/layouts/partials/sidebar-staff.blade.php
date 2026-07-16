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
        <a href="{{ route('staff.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium text-red-100 hover:bg-red-800 hover:text-white rounded-md transition-colors duration-200">
            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h7.5"/></svg>
            Home
        </a>
        <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2 text-sm font-medium text-red-100 hover:bg-red-800 hover:text-white rounded-md transition-colors duration-200">
            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
            Profile
        </a>
        <a href="{{ route('staff.permohonan.index') }}" class="flex items-center px-3 py-2 text-sm font-medium text-red-100 hover:bg-red-800 hover:text-white rounded-md transition-colors duration-200">
            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Daftar Permohonan
        </a>
        <div x-data="{ open: {{ request()->routeIs('staff.layanan.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium text-red-100 hover:bg-red-800 rounded-md transition-colors duration-200">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
                    <span>Kelola Layanan & Syarat</span>
                </div>
                <svg class="w-4 h-4 transition-transform flex-shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="open" x-transition class="mt-2 pl-8 space-y-2">
                <a href="{{ route('staff.layanan.index', ['type' => 'notaris']) }}" 
                class="block text-sm text-red-200 hover:text-white @if(request('type') == 'notaris') font-bold @endif">
                    Layanan Notaris
                </a>
                <a href="{{ route('staff.layanan.index', ['type' => 'ppat']) }}" 
                class="block text-sm text-red-200 hover:text-white @if(request('type') == 'ppat') font-bold @endif">
                    Layanan PPAT
                </a>
            </div>
        </div>
        <a href="{{ route('staff.laporan.index') }}" class="flex items-center px-3 py-2 text-sm font-medium text-red-100 hover:bg-red-800 hover:text-white rounded-md transition-colors duration-200">
            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 100 15 7.5 7.5 0 000-15zM21 21l-5.197-5.197"/></svg>
            Laporan
        </a>
        <a href="{{ route('staff.arsip.index') }}" class="flex items-center px-3 py-2 text-sm font-medium text-red-100 hover:bg-red-800 hover:text-white rounded-md transition-colors duration-200">
            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125-1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
            Arsip
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