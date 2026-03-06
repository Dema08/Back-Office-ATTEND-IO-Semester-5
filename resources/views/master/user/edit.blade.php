@extends('layouts.main')

@section('title', 'Edit User')
@section('page_title', 'Edit User')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Form Edit User</h2>
        <a href="{{ route('master.user.index') }}"
           class="inline-flex items-center rounded-xl border border-slate-700 bg-slate-900/60 px-3 py-2 text-xs font-medium text-slate-200 hover:bg-slate-800">
            ← Kembali ke Master User
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-rose-500/40 bg-rose-500/10 px-4 py-2 text-sm text-rose-200">
            <p class="font-semibold mb-1">Terjadi kesalahan:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 p-5 max-w-xl">
        <form action="{{ route('master.user.update', $user) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Nama --}}
            <div>
                <label class="block text-xs text-slate-400 mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/70">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-xs text-slate-400 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/70">
            </div>

            {{-- Password Baru --}}
            <div x-data="{ show: false }">
                <label for="password" class="block text-xs text-slate-400 mb-1">Password Baru</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'"
                           name="password" id="password"
                           class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 pr-10 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/70">
                    <button type="button"
                            @click="show = !show"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-500 hover:text-slate-200">
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
                <p class="mt-1 text-[11px] text-slate-500">
                    Kosongkan jika tidak ingin mengganti password.
                </p>
            </div>

            {{-- Konfirmasi Password Baru --}}
            <div x-data="{ show: false }">
                <label for="password_confirmation" class="block text-xs text-slate-400 mb-1">
                    Konfirmasi Password Baru
                </label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'"
                           name="password_confirmation" id="password_confirmation"
                           class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 pr-10 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/70">
                    <button type="button"
                            @click="show = !show"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-500 hover:text-slate-200">
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
                <p class="mt-1 text-[11px] text-slate-500">
                    Isi hanya jika mengubah password.
                </p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <a href="{{ route('master.user.index') }}"
                   class="text-xs text-slate-400 hover:text-slate-200">
                    Batalkan
                </a>
                <button type="submit"
                        class="inline-flex items-center rounded-xl bg-indigo-500 px-4 py-2 text-xs font-semibold text-white shadow-lg shadow-indigo-500/40">
                    💾 Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
