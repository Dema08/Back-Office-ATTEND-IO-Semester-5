@extends('layouts.main')

@section('title', 'Tambah Dosen')
@section('page_title', 'Tambah Dosen')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Form Tambah Dosen</h2>
        <a href="{{ route('master.dosen.index') }}"
           class="inline-flex items-center rounded-xl border border-slate-700 bg-slate-900/60 px-3 py-2 text-xs font-medium text-slate-200 hover:bg-slate-800">
            ← Kembali ke Master Dosen
        </a>
    </div>

    {{-- Error validation --}}
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
        <form action="{{ route('master.dosen.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- NIP --}}
            <div>
                <label for="nip" class="block text-xs text-slate-400 mb-1">NIP</label>
                <input type="text" name="nip" id="nip"
                       value="{{ old('nip') }}"
                       required
                       class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/70">
            </div>

            {{-- Nama Dosen --}}
            <div>
                <label for="nama_dosen" class="block text-xs text-slate-400 mb-1">Nama Dosen</label>
                <input type="text" name="nama_dosen" id="nama_dosen"
                       value="{{ old('nama_dosen') }}"
                       required
                       class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/70">
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs text-slate-400 mb-1">
                    Email <span class="text-slate-500">(opsional)</span>
                </label>
                <input type="email" name="email" id="email"
                       value="{{ old('email') }}"
                       class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/70">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <a href="{{ route('master.dosen.index') }}"
                   class="text-xs text-slate-400 hover:text-slate-200">
                    Batalkan
                </a>
                <button type="submit"
                        class="inline-flex items-center rounded-xl bg-indigo-500 px-4 py-2 text-xs font-semibold text-white shadow-lg shadow-indigo-500/40">
                    💾 Simpan
                </button>
            </div>
        </form>
    </div>
@endsection
