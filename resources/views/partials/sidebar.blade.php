<aside id="sidebar"
    class="flex flex-col bg-[#020617] border-r border-slate-800 transition-all duration-200 relative overflow-visible"
    :class="sidebarExpanded ? 'w-72' : 'w-20'">

    {{-- Logo + Toggle --}}
    <div class="px-4 pt-6 pb-4 flex items-center justify-between">
        <button type="button" @click="sidebarExpanded = !sidebarExpanded"
            class="flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-700 bg-slate-900/60 hover:bg-slate-800">
            <span class="text-lg">☰</span>
            <span class="font-semibold tracking-tight hidden sm:inline" x-show="sidebarExpanded" x-transition>
                ATTEND<span class="text-indigo-400">-IO</span>
            </span>
        </button>
    </div>

    {{-- Search --}}
    <div class="px-4 pb-4" x-show="sidebarExpanded" x-transition>
        <label class="relative block">
            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="7"></circle>
                    <line x1="16.5" y1="16.5" x2="21" y2="21"></line>
                </svg>
            </span>
            <input
                class="w-full rounded-xl bg-slate-900/70 border border-slate-700/80 px-9 py-2 text-sm placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/70"
                placeholder="Search for..." type="search">
        </label>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-2 py-2 space-y-6 text-sm text-slate-300">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="flex items-center px-4 py-2 rounded-lg hover:bg-slate-800/80 transition
                  {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-indigo-400' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h4m10-11v10a1 1 0 01-1 1h-4" />
            </svg>
            <span x-show="sidebarExpanded" x-transition>Dashboard</span>
        </a>

        {{-- Class Assist --}}
        <a href="{{ route('pertemuan.create') }}"
           class="flex items-center px-4 py-2 rounded-lg hover:bg-slate-800/80 transition
                  {{ request()->routeIs('pertemuan.*') ? 'bg-slate-800 text-indigo-400' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M8 7V3m8 4V3m-9 8h10m-9 4h9m-6 4h3" />
            </svg>
            <span x-show="sidebarExpanded" x-transition>Class Assist</span>
        </a>

        {{-- Master (Dropdown) --}}
        <div x-data="{ open: {{ request()->routeIs('master.*') ? 'true' : 'false' }} }" class="text-slate-300 relative">
            <button type="button" @click="open = !open"
                    class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-slate-800/80 transition">
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-0" :class="sidebarExpanded ? 'mr-3' : ''"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <span x-show="sidebarExpanded" x-transition>Master Data</span>
                </span>

                <svg x-show="sidebarExpanded" :class="{ 'rotate-90': open }" xmlns="http://www.w3.org/2000/svg"
                     class="h-4 w-4 transform transition-transform text-slate-400" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <div x-show="open" @click.outside="open = false" x-transition
                 :class="sidebarExpanded
                    ? 'ml-8 mt-2 space-y-1 text-slate-400'
                    : 'absolute left-full top-0 ml-3 bg-slate-900/95 border border-slate-700 rounded-xl shadow-lg px-3 py-2 space-y-1 text-slate-300 w-48 z-[9999]'">

                {{-- 1) Dosen --}}
                <a href="{{ route('master.dosen.index') }}"
                   class="block px-2 py-1.5 rounded hover:text-indigo-400 {{ request()->routeIs('master.dosen.*') ? 'text-indigo-400' : '' }}">
                    👨‍🏫 Dosen
                </a>

                {{-- 2) Mata Kuliah --}}
                <a href="{{ route('master.matakuliah.index') }}"
                   class="block px-2 py-1.5 rounded hover:text-indigo-400 {{ request()->routeIs('master.matakuliah.*') ? 'text-indigo-400' : '' }}">
                    📘 Mata Kuliah
                </a>

                {{-- 3) Kelas --}}
                <a href="{{ route('master.kelas.index') }}"
                   class="block px-2 py-1.5 rounded hover:text-indigo-400 {{ request()->routeIs('master.kelas.*') ? 'text-indigo-400' : '' }}">
                    🏫 Kelas
                </a>

                {{-- 4) Mahasiswa --}}
                <a href="{{ route('master.mahasiswa.index') }}"
                   class="block px-2 py-1.5 rounded hover:text-indigo-400 {{ request()->routeIs('master.mahasiswa.*') ? 'text-indigo-400' : '' }}">
                    👨‍🎓 Mahasiswa
                </a>

                {{-- 5) User --}}
                <a href="{{ route('master.user.index') }}"
                   class="block px-2 py-1.5 rounded hover:text-indigo-400 {{ request()->routeIs('master.user.*') ? 'text-indigo-400' : '' }}">
                    👤 User
                </a>
            </div>
        </div>

        {{-- Laporan --}}
        <a href="{{ route('laporan.index') }}"
           class="flex items-center px-4 py-2 rounded-lg hover:bg-slate-800/80 transition
                  {{ request()->routeIs('laporan.*') ? 'bg-slate-800 text-indigo-400' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 17v-6h13M9 5h13M4 19h1m0 0V5a2 2 0 012-2h14" />
            </svg>
            <span x-show="sidebarExpanded" x-transition>Laporan</span>
        </a>
    </nav>

    {{-- Bottom card / footer sidebar --}}
    <div class="px-4 py-5 border-t border-slate-800/80">
        <div class="text-xs text-slate-500 text-center" x-show="sidebarExpanded">
            © ATTEND-IO 2025
        </div>
    </div>
</aside>
