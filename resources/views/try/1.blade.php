<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>ATTEND-IO Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
        });
    </script>

</head>

<body class="min-h-screen bg-[#020617] text-slate-100">

    <div class="min-h-screen flex bg-[#020617]">
        {{-- SIDEBAR --}}
        <aside id="sidebar" class="hidden lg:flex w-72 flex-col bg-[#020617] border-r border-slate-800">
            {{-- Logo + Search --}}
            <div class="px-6 pt-6 pb-4 flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Dash</p>
                    <p class="text-xl font-semibold tracking-tight">ATTEND<span class="text-indigo-400">-IO</span></p>
                </div>
            </div>

            <div class="px-4 pb-4">
                <label class="relative block">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
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
            <nav class="flex-1 overflow-y-auto px-2 py-2 space-y-6 text-sm">
                <div>
                    <p class="px-4 mb-2 text-xs font-semibold tracking-wide text-slate-500 uppercase">Main</p>
                    <a href="#"
                        class="flex items-center gap-3 px-4 py-2 rounded-xl bg-indigo-500/10 text-indigo-300 border border-indigo-500/40">
                        <span
                            class="h-6 w-6 rounded-lg bg-indigo-500/20 flex items-center justify-center text-xs">🏠</span>
                        <span>Dashboard</span>
                    </a>
                    <a href="#"
                        class="mt-1 flex items-center gap-3 px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-900/80">
                        <span class="h-6 w-6 rounded-lg bg-slate-800 flex items-center justify-center text-xs">📄</span>
                        <span>All pages</span>
                    </a>
                    <a href="#"
                        class="mt-1 flex items-center gap-3 px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-900/80">
                        <span class="h-6 w-6 rounded-lg bg-slate-800 flex items-center justify-center text-xs">📊</span>
                        <span>Reports</span>
                    </a>
                    <a href="#"
                        class="mt-1 flex items-center gap-3 px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-900/80">
                        <span
                            class="h-6 w-6 rounded-lg bg-slate-800 flex items-center justify-center text-xs">🧑‍🏫</span>
                        <span>Classes</span>
                    </a>
                </div>

                <div>
                    <p class="px-4 mb-2 text-xs font-semibold tracking-wide text-slate-500 uppercase">Features</p>
                    <a href="#"
                        class="mt-1 flex items-center gap-3 px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-900/80">
                        <span class="h-6 w-6 rounded-lg bg-slate-800 flex items-center justify-center text-xs">✅</span>
                        <span>Tasks</span>
                    </a>
                    <a href="#"
                        class="mt-1 flex items-center gap-3 px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-900/80">
                        <span class="h-6 w-6 rounded-lg bg-slate-800 flex items-center justify-center text-xs">⭐</span>
                        <span>Features</span>
                    </a>
                    <a href="#"
                        class="mt-1 flex items-center gap-3 px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-900/80">
                        <span class="h-6 w-6 rounded-lg bg-slate-800 flex items-center justify-center text-xs">💳</span>
                        <span>Billing</span>
                    </a>
                </div>

                <div>
                    <p class="px-4 mb-2 text-xs font-semibold tracking-wide text-slate-500 uppercase">System</p>
                    <a href="#"
                        class="mt-1 flex items-center gap-3 px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-900/80">
                        <span class="h-6 w-6 rounded-lg bg-slate-800 flex items-center justify-center text-xs">⚙️</span>
                        <span>Settings</span>
                    </a>
                    <a href="#"
                        class="mt-1 flex items-center gap-3 px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-900/80">
                        <span class="h-6 w-6 rounded-lg bg-slate-800 flex items-center justify-center text-xs">📦</span>
                        <span>Integrations</span>
                    </a>
                </div>
            </nav>

            {{-- Bottom card --}}
            <div class="px-4 py-5 border-t border-slate-800/80">
                <button
                    class="w-full rounded-xl bg- gradient-to-r from-indigo-500 to-fuchsia-500 text-sm font-medium py-2.5 text-center shadow-lg shadow-indigo-500/30">
                    Get template →
                </button>

                <div class="mt-4 flex items-center gap-3">
                    <div
                        class="h-9 w-9 rounded-full bg- gradient-to-br from-indigo-500 to-fuchsia-500 flex items-center justify-center text-xs font-semibold">
                        JC
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium">John Carter</p>
                        <p class="text-xs text-slate-500">Account settings</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- MAIN CONTENT --}}
        <main class="flex-1 flex flex-col">
            {{-- Top bar --}}
            <header class="px-4 sm:px-6 lg:px-8 py-4 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    {{-- TOMBOL LOGO / TOGGLE SIDEBAR --}}
                    <button id="sidebarToggle"
                        class="flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-700 bg-slate-900/60 hover:bg-slate-800">
                        {{-- Bisa pakai ikon saja atau teks ATTEND-IO --}}
                        <span class="text-lg">☰</span>
                        <span class="font-semibold tracking-tight hidden sm:inline">ATTEND<span
                                class="text-indigo-400">-IO</span></span>
                    </button>

                    <div>
                        <p class="text-xs text-slate-400">Welcome back, John</p>
                        <h1 class="text-xl sm:text-2xl font-semibold tracking-tight">
                            Measure your class engagement & attendance.
                        </h1>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        class="hidden sm:inline-flex items-center rounded-xl border border-slate-700 bg-slate-900/60 px-3 py-2 text-xs font-medium text-slate-200 hover:bg-slate-800">
                        Export data
                    </button>
                    <button
                        class="inline-flex items-center rounded-xl bg -gradient-to-r from-indigo-500 to-fuchsia-500 px-4 py-2 text-xs font-semibold shadow-lg shadow-indigo-500/40">
                        Create report
                    </button>
                </div>
            </header>


            {{-- Content --}}
            <div class="flex-1 px-4 sm:px-6 lg:px-8 py-6 space-y-6 overflow-y-auto">

                {{-- Top KPIs --}}
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    {{-- Card 1 --}}
                    <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs text-slate-400">Total pageviews</p>
                                <p class="mt-2 text-2xl font-semibold">50.8K</p>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full bg-emerald-500/10 text-emerald-400">
                                +24.8%
                            </span>
                        </div>
                    </div>

                    {{-- Card 2 --}}
                    <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs text-slate-400">Monthly users</p>
                                <p class="mt-2 text-2xl font-semibold">23.6K</p>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full bg-rose-500/10 text-rose-400">
                                -8.2%
                            </span>
                        </div>
                    </div>

                    {{-- Card 3 --}}
                    <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs text-slate-400">New sign ups</p>
                                <p class="mt-2 text-2xl font-semibold">756</p>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full bg-emerald-500/10 text-emerald-400">
                                +31.1%
                            </span>
                        </div>
                    </div>

                    {{-- Card 4 --}}
                    <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs text-slate-400">Subscriptions</p>
                                <p class="mt-2 text-2xl font-semibold">2.3K</p>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full bg-emerald-500/10 text-emerald-400">
                                +13.5%
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Middle: big chart + side charts --}}
                <div class="grid gap-4 xl:grid-cols-3">
                    {{-- Big revenue card --}}
                    <div
                        class="xl:col-span-2 rounded-2xl bg-slate-900/70 border border-slate-800/80 p-5 flex flex-col">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-slate-400">Total revenue</p>
                                <p class="mt-1 text-2xl font-semibold">$240.8K</p>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full bg-emerald-500/10 text-emerald-400">
                                +24.5%
                            </span>
                        </div>

                        <div class="mt-4 flex items-center gap-4 text-xs text-slate-400">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-indigo-400"></span>
                                Revenue
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-fuchsia-400"></span>
                                Expenses
                            </div>
                            <div class="ml-auto text-[11px] text-slate-500">
                                Jan 2024 – Dec 2024
                            </div>
                        </div>

                        {{-- Fake chart --}}
                        <div
                            class="mt-5 flex-1 rounded-2xl bg - gradient-to-br from-slate-900 via-slate-900 to-slate-950 border border-slate-800/80 relative overflow-hidden">
                            <div
                                class="absolute inset-0 opacity-60 bg - [radial-gradient(circle_at_top,_rgba(129,140,248,0.4),_transparent_55%),radial-gradient(circle_at_bottom,_rgba(244,114,182,0.35),_transparent_55%)]">
                            </div>
                            <div class="relative h-full flex items-end px-4 pb-4 gap-2">
                                @for ($i = 0; $i < 12; $i++)
                                    <div class="flex-1 flex flex-col justify-end gap-1">
                                        <div class="h-20 rounded-full bg-indigo-400/40"></div>
                                        <div class="h-10 rounded-full bg-fuchsia-400/40"></div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>

                    {{-- Right small charts --}}
                    <div class="space-y-4">
                        <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 p-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-slate-400">Total profit</p>
                                    <p class="mt-1 text-xl font-semibold">$144.6K</p>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full bg-emerald-500/10 text-emerald-400">
                                    +23.8%
                                </span>
                            </div>

                            <div class="mt-4 h-24 flex items-end justify-between gap-1">
                                @for ($i = 0; $i < 18; $i++)
                                    <div class="flex-1 flex flex-col justify-end gap-1">
                                        <div class="h-10 rounded-full bg-indigo-400/60"></div>
                                        <div class="h-6 rounded-full bg-fuchsia-400/60"></div>
                                    </div>
                                @endfor
                            </div>
                            <p class="mt-3 text-[11px] text-slate-500">Last 12 months • View report</p>
                        </div>

                        <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 p-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-slate-400">Total sessions</p>
                                    <p class="mt-1 text-xl font-semibold">400</p>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full bg-emerald-500/10 text-emerald-400">
                                    +19.1%
                                </span>
                            </div>

                            <div class="mt-4 h-20 flex items-end gap-3">
                                @foreach ([40, 60, 35, 75, 55] as $h)
                                    <div class="flex-1 flex flex-col justify-end gap-1">
                                        <div class="h-3 rounded-full bg-slate-700/80"></div>
                                        <div class="rounded-full bg-indigo-400/60"
                                            style="height: {{ $h }}%;"></div>
                                    </div>
                                @endforeach
                            </div>
                            <p class="mt-3 text-[11px] text-slate-500">10k visitors • View report</p>
                        </div>
                    </div>
                </div>

                {{-- Bottom row --}}
                <div class="grid gap-4 xl:grid-cols-3">
                    {{-- Gauge --}}
                    <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 p-5 flex flex-col">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold">Reports overview</p>
                            <button class="text-[11px] text-slate-400 flex items-center gap-1">
                                Select date
                                <span>▾</span>
                            </button>
                        </div>

                        <div class="mt-5 flex flex-col items-center">
                            {{-- fake semicircle gauge --}}
                            <div
                                class="w-40 h-20 rounded-t-full border -[10px] border-t-transparent border-l-fuchsia-500 border-r-indigo-500 border-b-indigo-500/40 relative">
                                <div
                                    class="absolute inset-x-0 bottom -[-10px] mx-auto w-20 h-10 rounded-t-full bg-slate-900">
                                </div>
                            </div>
                            <p class="mt-4 text-3xl font-semibold">23,648</p>
                            <p class="text-xs text-slate-400">Users by device</p>

                            <div class="mt-4 w-full text-xs space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-full bg-indigo-400"></span>
                                        Desktop users
                                    </div>
                                    <span>15,624</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-full bg-fuchsia-400"></span>
                                        Phone app users
                                    </div>
                                    <span>5,546</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-full bg-sky-400"></span>
                                        Laptop users
                                    </div>
                                    <span>2,478</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Recent orders --}}
                    <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 p-5 flex flex-col">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold">Recent orders</p>
                            <p class="text-[11px] text-slate-400">Jan 2024</p>
                        </div>

                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full text-xs">
                                <thead class="text-slate-400">
                                    <tr class="border-b border-slate-800/80">
                                        <th class="py-2 text-left font-medium">Order</th>
                                        <th class="py-2 text-left font-medium">Date</th>
                                        <th class="py-2 text-left font-medium">Status</th>
                                        <th class="py-2 text-right font-medium">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/80">
                                    @foreach ([['#1520', 'Dec 29, 10:06 AM', 'Paid', '$329.40', 'emerald'], ['#1521', 'Dec 29, 01:09 AM', 'Pending', '$77.42', 'amber'], ['#1522', 'Dec 29, 12:54 AM', 'Pending', '$52.36', 'amber'], ['#1523', 'Dec 28, 3:22 PM', 'Paid', '$930.12', 'emerald'], ['#1524', 'Dec 27, 2:20 PM', 'Paid', '$248.12', 'emerald'], ['#1527', 'Dec 26, 0:48 AM', 'Paid', '$46.00', 'emerald']] as $row)
                                        <tr>
                                            <td class="py-2 pr-2">{{ $row[0] }}</td>
                                            <td class="py-2 pr-2 text-slate-400">{{ $row[1] }}</td>
                                            <td class="py-2 pr-2">
                                                @php $color = $row[4] === 'emerald' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400'; @endphp
                                                <span class="text-[11px] px-2 py-1 rounded-full {{ $color }}">
                                                    {{ $row[2] }}
                                                </span>
                                            </td>
                                            <td class="py-2 pl-2 text-right">{{ $row[3] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Users by country / map --}}
                    <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 p-5 flex flex-col">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold">Users by country</p>
                            <button class="text-[11px] text-slate-400 flex items-center gap-1">
                                Export
                                <span>⬇</span>
                            </button>
                        </div>

                        <div class="mt-4 flex-1 flex flex-col gap-4">
                            <div class="space-y-2 text-xs">
                                @foreach ([['United States', '30%'], ['United Kingdom', '20%'], ['Canada', '20%'], ['Australia', '15%'], ['Spain', '15%']] as $c)
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-300">{{ $c[0] }}</span>
                                        <span class="text-slate-400">{{ $c[1] }}</span>
                                    </div>
                                @endforeach
                            </div>

                            {{-- simple "map" --}}
                            <div
                                class="mt-2 flex-1 rounded-2xl bg-slate-950 border border-slate-800/80 relative overflow-hidden">
                                <div
                                    class="absolute inset-0 opacity-60 bg-[radial-gradient(circle_at_20%_30%,rgba(129,140,248,0.5),transparent_55%),radial-gradient(circle_at_60%_60%,rgba(244,114,182,0.45),transparent_55%),radial-gradient(circle_at_80%_20%,rgba(45,212,191,0.45),transparent_55%)]">
                                </div>
                                <div class="relative h-full flex items-center justify-center">
                                    <p class="text-xs text-slate-500">World map placeholder</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

</body>

</html>
