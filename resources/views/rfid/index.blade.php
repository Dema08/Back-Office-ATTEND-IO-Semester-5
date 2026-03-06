@extends('layouts.main')

@section('title', 'Kelola RFID')
@section('page_title', 'Kelola Kartu RFID')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-semibold">Kelola Kartu RFID</h2>
            <p class="text-xs text-slate-400">
                Lihat dan kelola relasi kartu RFID dengan mahasiswa.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('master.mahasiswa.index') }}"
               class="hidden sm:inline-flex items-center rounded-xl border border-slate-700 bg-slate-900/60 px-3 py-2 text-xs font-medium text-slate-200 hover:bg-slate-800">
                ← Kembali ke Mahasiswa
            </a>
            <a href="{{ route('rfid.create') }}"
               class="inline-flex items-center rounded-xl bg-indigo-500 px-4 py-2 text-xs font-semibold text-white shadow-lg shadow-indigo-500/40">
                ➕ Daftarkan RFID
            </a>
        </div>
    </div>

    {{-- Tabel kartu RFID terdaftar --}}
    <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 overflow-x-auto">
        <table class="min-w-full text-xs">
            <thead class="bg-slate-900/90 text-slate-400">
                <tr class="border-b border-slate-800/80">
                    <th class="px-4 py-2 text-left font-medium">No</th>
                    <th class="px-4 py-2 text-left font-medium">UID</th>
                    <th class="px-4 py-2 text-left font-medium">NIM</th>
                    <th class="px-4 py-2 text-left font-medium">Nama</th>
                    <th class="px-4 py-2 text-left font-medium">Golongan</th>
                    <th class="px-4 py-2 text-left font-medium">Aksi</th> {{-- ⬅️ baru --}}
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80 text-slate-200">
                @foreach ($rfids as $idx => $rfid)
                    <tr>
                        <td class="px-4 py-2">{{ $idx + 1 }}</td>
                        <td class="px-4 py-2 font-mono text-xs">{{ $rfid->uid_hex }}</td>
                        <td class="px-4 py-2">{{ $rfid->mahasiswa->nim ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $rfid->mahasiswa->nama ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $rfid->mahasiswa->golongan ?? '-' }}</td>
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-2">
                                {{-- Edit: lempar ke edit mahasiswa, biar UID bisa diubah di sana --}}
                                @if ($rfid->mahasiswa)
                                    <a href="{{ route('master.mahasiswa.edit', $rfid->mahasiswa->id) }}"
                                       class="text-[11px] text-indigo-400 hover:text-indigo-300">
                                        ✏️ Edit
                                    </a>
                                @endif

                                {{-- Hapus RFID --}}
                                <form action="{{ route('rfid.destroy', $rfid->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus relasi RFID ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-[11px] text-rose-400 hover:text-rose-300">
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    function setRfidMode(mode) {
        fetch("{{ url('/api/attendio/rfid-mode') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ mode })
        }).catch(err => console.error('Gagal set mode RFID:', err));
    }

    // Masuk halaman Kelola RFID → mode register
    setRfidMode('register');

    // Keluar halaman ini → balik ke attendance
    window.addEventListener('beforeunload', () => {
        setRfidMode('attendance');
    });
});
</script>
@endpush
