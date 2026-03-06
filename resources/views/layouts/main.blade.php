<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ATTEND-IO')</title>

    {{-- Tailwind via CDN (tanpa Vite) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine.js --}}
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    @stack('styles')
</head>

<body class="min-h-screen bg-[#020617] text-slate-100 flex"
      x-data="{ sidebarExpanded: JSON.parse(localStorage.getItem('sidebarExpanded') ?? 'true') }"
      x-init="$watch('sidebarExpanded', value => {
          localStorage.setItem('sidebarExpanded', JSON.stringify(value));
      })">

    @auth
        {{-- ====================== LAYOUT SAAT SUDAH LOGIN ====================== --}}

        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Main area: appbar + content + footer --}}
        <div class="flex-1 flex flex-col min-h-screen transition-all duration-200 relative z-10">

            {{-- Appbar --}}
            @include('partials.appbar')

            {{-- Konten halaman --}}
            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
                @yield('content')
            </main>

            {{-- Footer --}}
            @include('partials.footer')
        </div>
    @else
        {{-- ====================== LAYOUT SAAT BELUM LOGIN (LOGIN / REGISTER) ====================== --}}

        <div class="flex-1 flex items-center justify-center px-4 py-8">
            {{-- Halaman auth akan dirender di sini --}}
            @yield('content')
        </div>
    @endauth

    @stack('scripts')
</body>

</html>
