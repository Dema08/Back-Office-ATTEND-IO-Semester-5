<header class="px-4 sm:px-6 lg:px-8 py-4 border-b border-slate-800 flex items-center justify-between">
    {{-- Bagian kiri: Judul halaman dan sapaan --}}
    <div class="flex items-center gap-3">
        <div>
            @auth
                <p class="text-xs text-slate-400">
                    Welcome back, {{ Auth::user()->name ?? 'User' }}
                </p>
            @else
                <p class="text-xs text-slate-400">Welcome to ATTEND-IO</p>
            @endauth

            <h1 class="text-xl sm:text-2xl font-semibold tracking-tight">
                @yield('page_title', 'Dashboard')
            </h1>
        </div>
    </div>

    {{-- Bagian kanan: Tombol aksi / logout --}}
    @auth
        <div class="flex items-center gap-3">
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-1 rounded-xl border border-slate-700 bg-slate-900/60 px-3 py-2 text-xs font-medium text-slate-200 hover:bg-slate-800 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    @endauth
</header>
