@extends('layouts.main')

@section('title', 'Master Mahasiswa')
@section('page_title', 'Master Mahasiswa')

@section('content')

<div class="mb-4 flex flex-wrap items-center gap-3">
    {{-- Search nama / NIM --}}
    <form method="GET"
          action="{{ route('master.mahasiswa.index') }}"
          class="flex flex-wrap items-center gap-2">

        <div>
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari nama atau NIM..."
                   class="rounded-xl bg-slate-900 border border-slate-700 px-3 py-1.5 text-xs text-slate-100 w-56">
        </div>

        <div>
            <select name="golongan"
                    class="rounded-xl bg-slate-900 border border-slate-700 px-3 py-1.5 text-xs text-slate-100">
                <option value="">Semua Golongan</option>
                @foreach ($golonganList as $g)
                    <option value="{{ $g }}" @selected(request('golongan') == $g)>
                        {{ $g }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit"
                class="rounded-xl bg-indigo-500 px-3 py-1.5 text-xs font-semibold text-white">
            🔍 Cari
        </button>

        @if (request('search') || request('golongan'))
            <a href="{{ route('master.mahasiswa.index') }}"
               class="text-[11px] text-slate-400 hover:text-slate-200">
                Reset
            </a>
        @endif
    </form>
</div>

    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-semibold">Daftar Mahasiswa</h2>
            <p class="text-xs text-slate-400">
                Kelola data mahasiswa yang terdaftar dalam sistem.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('master.index') }}"
               class="hidden sm:inline-flex items-center rounded-xl border border-slate-700 bg-slate-900/60 px-3 py-2 text-xs font-medium text-slate-200 hover:bg-slate-800">
                ← Kembali ke Master
            </a>

            {{-- Tambah mahasiswa --}}
            <a href="{{ route('master.mahasiswa.create') }}"
               class="inline-flex items-center rounded-xl bg-indigo-500 px-4 py-2 text-xs font-semibold text-white shadow-lg shadow-indigo-500/40">
                ➕ Tambah Mahasiswa
            </a>

            {{-- Opsional: halaman khusus kelola RFID --}}
            {{-- Pastikan route('rfid.index') sudah ada, kalau belum bisa dihapus --}}
            <a href="{{ route('rfid.index') }}"
               class="hidden sm:inline-flex items-center rounded-xl bg-amber-500 px-3 py-2 text-xs font-semibold text-white shadow-lg shadow-amber-500/30">
                🎫 Kelola RFID
            </a>
        </div>
    </div>

    @if ($mahasiswa->isEmpty())
        <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 px-4 py-6 text-sm text-slate-400">
            Belum ada data mahasiswa. Klik <span class="font-semibold text-slate-200">“Tambah Mahasiswa”</span> untuk menambahkan.
        </div>
    @else
        <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead class="bg-slate-900/90 text-slate-400">
                    <tr class="border-b border-slate-800/80">
                        <th class="px-4 py-2 text-left font-medium">No</th>
                        <th class="px-4 py-2 text-left font-medium">NIM</th>
                        <th class="px-4 py-2 text-left font-medium">Nama</th>
                        <th class="px-4 py-2 text-left font-medium">Email</th>
                        <th class="px-4 py-2 text-left font-medium">Golongan</th>
                        <th class="px-4 py-2 text-left font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80 text-slate-200">
                    @foreach ($mahasiswa as $index => $m)
                        <tr>
                            <td class="px-4 py-2 align-top">
                                {{ $mahasiswa->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-2 align-top font-mono text-xs">
                                {{ $m->nim }}
                            </td>
                            <td class="px-4 py-2 align-top">
                                {{ $m->nama }}
                            </td>
                            <td class="px-4 py-2 align-top">
                                {{ $m->email ?? '-' }}
                            </td>
                            <td class="px-4 py-2 align-top">
                                {{ $m->golongan ?? '-' }}
                            </td>
                            <td class="px-4 py-2 align-top">
                                <div class="flex flex-wrap items-center gap-2">
                                    {{-- Edit --}}
                                    <a href="{{ route('master.mahasiswa.edit', $m->id) }}"
                                       class="text-xs text-indigo-400 hover:text-indigo-300">
                                        ✏️ Edit
                                    </a>

                                    {{-- Hapus --}}
                                    <form action="{{ route('master.mahasiswa.destroy', $m->id) }}"
                                          method="POST"
                                          class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                class="delete-btn text-xs text-rose-400 hover:text-rose-300"
                                                data-nama="{{ $m->nama }}">
                                            🗑 Hapus
                                        </button>
                                    </form>

                                    {{-- Daftarkan / Update RFID untuk mahasiswa ini --}}
                                      @if (!$m->rfidTag)
                                        <a href="{{ route('rfid.create', ['mahasiswa_id' => $m->id]) }}"
                                        class="inline-flex items-center rounded-lg bg-amber-500/80 px-2 py-1 text-[11px] text-white">
                                            🎫 Daftarkan RFID
                                        </a>
                                    @else
                                        <span class="inline-flex items-center rounded-lg bg-emerald-500/10 px-2 py-1 text-[11px] text-emerald-300 border border-emerald-500/40">
                                            ✅ RFID: {{ $m->rfidTag->uid_hex }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $mahasiswa->links() }}
        </div>
    @endif
@endsection

@push('scripts')
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Pop-up konfirmasi hapus
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function () {
                const form = this.closest('form');
                const nama = this.dataset.nama;

                Swal.fire({
                    title: 'Hapus Data?',
                    text: `Apakah kamu yakin ingin menghapus mahasiswa "${nama}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    background: '#0f172a',
                    color: '#e2e8f0'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Notifikasi sukses
        @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 2000,
            showConfirmButton: false,
            background: '#0f172a',
            color: '#e2e8f0'
        });
        @endif
    </script>
@endpush
