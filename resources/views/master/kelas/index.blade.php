@extends('layouts.main')

@section('title', 'Master Kelas')
@section('page_title', 'Master Kelas')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-semibold">Daftar Kelas</h2>
            <p class="text-xs text-slate-400">
                Kelola data kelas, dosen pengampu, dan mata kuliah.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('master.index') }}"
               class="hidden sm:inline-flex items-center rounded-xl border border-slate-700 bg-slate-900/60 px-3 py-2 text-xs font-medium text-slate-200 hover:bg-slate-800">
                ← Kembali ke Master
            </a>
            <a href="{{ route('master.kelas.create') }}"
               class="inline-flex items-center rounded-xl bg-indigo-500 px-4 py-2 text-xs font-semibold text-white shadow-lg shadow-indigo-500/40">
                ➕ Tambah Kelas
            </a>
        </div>
    </div>

    @if ($kelas->isEmpty())
        <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 px-4 py-6 text-sm text-slate-400">
            Belum ada data kelas.
        </div>
    @else
        <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead class="bg-slate-900/90 text-slate-400">
                    <tr class="border-b border-slate-800/80">
                        <th class="px-4 py-2 text-left font-medium">No</th>
                        <th class="px-4 py-2 text-left font-medium">Golongan</th>
                        <th class="px-4 py-2 text-left font-medium">Mata Kuliah &amp; Dosen</th>
                        <th class="px-4 py-2 text-left font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80 text-slate-200">
    @forelse ($kelas as $golongan => $items)
        <tr>
            <td class="px-4 py-2 align-top">
                {{ $loop->iteration }}
            </td>

            <td class="px-4 py-2 align-top">
                {{ $golongan }}
            </td>

            {{-- Kolom Mata Kuliah & Dosen --}}
            <td class="px-4 py-2 align-top">
                <ul class="space-y-1">
                    @foreach ($items as $item)
                        <li>
                            {{ $item->mataKuliah->nama_matkul ?? '-' }}
                            <span class="text-slate-400">
                                — {{ $item->dosen->nama_dosen ?? '-' }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </td>

            {{-- Kolom Aksi --}}
            <td class="px-4 py-2 align-top">
                <ul class="space-y-1">
                    @foreach ($items as $item)
                        <li>
                            <a href="{{ route('master.kelas.edit', $item->id) }}"
                               class="text-[10px] text-indigo-400 hover:text-indigo-300">
                                ✏️ edit
                            </a>

                            <form action="{{ route('master.kelas.destroy', $item->id) }}"
                                  method="POST"
                                  class="inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        class="delete-btn text-[10px] text-rose-400 hover:text-rose-300"
                                        data-nama="Kelas {{ $golongan }} - {{ $item->mataKuliah->nama_matkul ?? '-' }}">
                                    🗑 hapus
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="px-4 py-4 text-center text-slate-400">
                Belum ada data kelas.
            </td>
        </tr>
    @endforelse
</tbody>

            </table>
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
                    text: `Apakah kamu yakin ingin menghapus ${nama}?`,
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
