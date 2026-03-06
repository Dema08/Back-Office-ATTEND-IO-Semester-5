@extends('layouts.main')

@section('title', 'Login')
@section('page_title', 'Login')

@section('content')
    <div class="flex items-center justify-center">
        <div class="w-full max-w-md rounded-2xl bg-slate-900/70 border border-slate-800/80 p-6">
            <h2 class="text-lg font-semibold text-slate-100 mb-2">Masuk ke ATTEND-IO</h2>
            <p class="text-xs text-slate-400 mb-4">
                Silakan login untuk mengakses dashboard dan fitur lainnya.
            </p>

            {{-- Notif sukses (misal setelah reset password) --}}
            @if (session('status'))
                <div class="mb-4 rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-2 text-xs text-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Error validasi / login gagal --}}
            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-rose-500/40 bg-rose-500/10 px-4 py-2 text-xs text-rose-200">
                    @foreach ($errors->all() as $err)
                        <p>{{ $err }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs text-slate-400 mb-1">Email</label>
                    <input type="email" name="email" id="email"
                           value="{{ old('email') }}" required autofocus
                           class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/70">
                </div>

                {{-- Password + tombol mata --}}
                <div x-data="{ show: false }">
                    <label for="password" class="block text-xs text-slate-400 mb-1">Password</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" name="password" id="password" required
                               class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 pr-10 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/70">

                        <button type="button"
                                @click="show = !show"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-500 hover:text-slate-200">
                            {{-- icon mata / mata tertutup --}}
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>

                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.106 7.25 19 12 19c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 5c4.75 0 8.774 2.894 10.066 7a10.523 10.523 0 01-4.293 5.245M6.228 6.228L3 3m3.228 3.228L9.88 9.88m4.24 4.24L21 21m-7.88-7.88a3 3 0 00-4.24-4.24" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- <div class="flex items-center justify-between text-xs text-slate-400">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="remember"
                               class="rounded border-slate-600 bg-slate-900 text-indigo-500">
                        <span>Ingat saya</span>
                    </label>
                </div> --}}
                    <button type="submit"
                            class="inline-flex items-center rounded-xl bg-indigo-500 px-4 py-2 text-xs font-semibold text-white shadow-lg shadow-indigo-500/40">
                        Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
