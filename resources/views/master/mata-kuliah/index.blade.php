@extends('layouts.main')

@section('title', 'Master Mata Kuliah')
@section('page_title', 'Master Mata Kuliah')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-semibold">Daftar Mata Kuliah</h2>
            <p class="text-xs text-slate-400">
                Kelola daftar mata kuliah yang tersedia.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('master.index') }}"
               class="hidden sm:inline-flex items-center rounded-xl border border-slate-700 bg-slate-900/60 px-3 py-2 text-xs font-medium text-slate-200 hover:bg-slate-800">
                ← Kembali ke Master
            </a>
            <a href="{{ route('master.matakuliah.create') }}"
               class="inline-flex items-center rounded-xl bg-indigo-500 px-4 py-2 text-xs font-semibold text-white shadow-lg shadow-indigo-500/40">
                ➕ Tambah Mata Kuliah
            </a>
        </div>
    </div>

    @if ($mataKuliah->isEmpty())
        <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 px-4 py-6 text-sm text-slate-400">
            Belum ada data mata kuliah.
        </div>
    @else
        <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead class="bg-slate-900/90 text-slate-400">
                    <tr class="border-b border-slate-800/80">
                        <th class="px-4 py-2 text-left font-medium">No</th>
                        <th class="px-4 py-2 text-left font-medium">Kode</th>
                        <th class="px-4 py-2 text-left font-medium">Nama Mata Kuliah</th>
                        <th class="px-4 py-2 text-left font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80 text-slate-200">
                    @foreach ($mataKuliah as $index => $mk)
                        <tr>
                            <td class="px-4 py-2 align-top">
                                {{ $mataKuliah->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-2 align-top font-mono text-xs">
                                {{ $mk->kode_matkul }}
                            </td>
                            <td class="px-4 py-2 align-top">
                                {{ $mk->nama_matkul }}
                            </td>
                            <td class="px-4 py-2 align-top">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('master.matakuliah.edit', $mk->id) }}"
                                       class="text-xs text-indigo-400 hover:text-indigo-300">
                                        ✏️ Edit
                                    </a>

                                    <form action="{{ route('master.matakuliah.destroy', $mk->id) }}"
                                          method="POST"
                                          class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                class="delete-btn text-xs text-rose-400 hover:text-rose-300"
                                                data-nama="{{ $mk->nama_matkul }}">
                                            🗑 Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $mataKuliah->links() }}
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
                    text: `Apakah kamu yakin ingin menghapus mata kuliah "${nama}"?`,
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

        // Notifikasi error (gagal hapus)
        @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal Menghapus!',
            text: '{{ session('error') }}',
            background: '#0f172a',
            color: '#e2e8f0',
            confirmButtonColor: '#ef4444'
        });
        @endif
    </script>
@endpush
