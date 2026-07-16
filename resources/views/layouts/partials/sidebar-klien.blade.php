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
            Hi, {{ optional(auth()->user())->name ?? 'Pengguna' }}!
        </p>
    </div>

    <nav class="flex-1 px-4 space-y-2">
        
        <a href="{{ route('klien.dashboard') }}" 
            class="flex items-center px-3 py-2 text-sm font-medium text-red-100 hover:bg-red-800 hover:text-white rounded-md transition">
            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h7.5"/></svg>
            Home
        </a>

        <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2 text-sm font-medium text-red-100 hover:bg-red-800 hover:text-white rounded-md">
            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
            Profile
        </a>
        
        <button @click.prevent="currentView = 'iva'; sidebarOpen = false"
                class="w-full flex items-center px-3 py-2 text-sm font-medium text-red-100 hover:bg-red-800 hover:text-white rounded-md transition">
            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
            </svg>
            Asisten Virtual (IVA)
        </button>

        <a href="{{ route('klien.permohonan.index') }}" class="flex items-center px-3 py-2 text-sm font-medium text-red-100 hover:bg-red-800 hover:text-white rounded-md">
            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Daftar Permohonan
        </a>
        <a href="{{ route('klien.permohonan.create') }}" class="flex items-center px-3 py-2 text-sm font-medium text-red-100 hover:bg-red-800 hover:text-white rounded-md">
            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            Ajukan Permohonan
        </a>
        <a href="{{ route('klien.syarat-informasi') }}" class="flex items-center px-3 py-2 text-sm font-medium text-red-100 hover:bg-red-800 hover:text-white rounded-md">
            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Syarat dan Informasi
        </a>
    </nav>

    <div class="p-4 mt-auto border-t border-red-800">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="#" onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center px-3 py-2 text-sm font-medium text-red-100 hover:bg-red-800 hover:text-white rounded-md">
                <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                Logout
            </a>
        </form>
    </div>
</div>