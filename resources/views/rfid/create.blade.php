@extends('layouts.main')

@section('title', 'Daftarkan RFID')
@section('page_title', 'Daftarkan Kartu RFID')

@section('content')
    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-rose-500/10 border border-rose-500/40 px-4 py-2 text-xs text-rose-200">
            @foreach ($errors->all() as $err)
                <p>{{ $err }}</p>
            @endforeach
        </div>
    @endif

    <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 p-4 max-w-xl">
        <h2 class="text-lg font-semibold mb-3">Daftarkan Kartu RFID</h2>

        <form action="{{ route('rfid.store') }}" method="POST" class="space-y-3">
            @csrf

            {{-- Pilih mahasiswa --}}
            <div>
                <label class="block text-xs text-slate-400 mb-1">Mahasiswa</label>
                <select name="mahasiswa_id" required
                        class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm">
                    <option value="">-- Pilih Mahasiswa --</option>
                    @foreach ($mahasiswa as $m)
                        <option value="{{ $m->id }}"
                            @selected(old('mahasiswa_id', $selectedMahasiswaId) == $m->id)>
                            {{ $m->nim }} - {{ $m->nama }} ({{ $m->golongan }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- UID RFID --}}
            <div>
                <label class="block text-xs text-slate-400 mb-1">
                    UID Kartu RFID
                </label>
                <input id="uid_hex"
                       type="text"
                       name="uid_hex"
                       required
                       value="{{ old('uid_hex') }}"
                       placeholder="Contoh: 7D 25 29 02"
                       class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm font-mono">
                <p class="text-[11px] text-slate-500 mt-1" id="uid_status">
                    Tempelkan kartu ke reader. UID akan terisi otomatis jika ESP sudah terkoneksi.
                </p>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <button id="btn_simpan" type="submit"
                        class="rounded-xl bg-indigo-500 px-4 py-2 text-sm font-semibold text-white">
                    Simpan
                </button>
                <a href="{{ route('rfid.index') }}"
                   class="text-xs text-slate-400 hover:text-slate-200">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const uidInput  = document.getElementById('uid_hex');
    const uidStatus = document.getElementById('uid_status');
    const btnSimpan = document.getElementById('btn_simpan');

    // ---------- helper format ----------
    function formatUid(uidHex) {
        // "7D252902" -> "7D 25 29 02"
        return uidHex.replace(/(..)/g, '$1 ').trim();
    }

    // ---------- API set mode ----------
    function setRfidMode(mode) {
        fetch("{{ url('/api/attendio/rfid-mode') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // kalau pakai sanctum di API bisa dihapus
            },
            body: JSON.stringify({ mode })
        }).catch(err => console.error('Gagal set mode RFID:', err));
    }

    // Saat HALAMAN INI dibuka → MODE = register
    setRfidMode('register');

    // Saat HALAMAN INI ditutup (pindah halaman / submit form) → MODE = attendance
    window.addEventListener('beforeunload', () => {
        setRfidMode('attendance');
    });

    // ---------- polling last scan dari ESP ----------
    function pollLastScan() {
        fetch("{{ url('/api/attendio/rfid-last-scan') }}")
            .then(res => res.json())
            .then(data => {
                if (data.status !== 'ok') return;

                const uidHex = data.uid_hex || '';
                if (!uidHex) return;

                const currentRaw = uidInput.dataset.raw || '';
                if (currentRaw === uidHex) return;   // tidak berubah

                uidInput.dataset.raw = uidHex;
                uidInput.value = formatUid(uidHex);

                if (data.registered) {
                    uidStatus.textContent =
                        `UID sudah terdaftar untuk ${data.mahasiswa.nim} - ${data.mahasiswa.nama}.`;
                    uidStatus.className = 'text-[11px] mt-1 text-rose-400';
                    btnSimpan.disabled = true;
                } else {
                    uidStatus.textContent =
                        'UID belum terdaftar. Siap disimpan untuk mahasiswa yang dipilih.';
                    uidStatus.className = 'text-[11px] mt-1 text-emerald-400';
                    btnSimpan.disabled = false;
                }
            })
            .catch(err => console.error('Gagal polling last-scan:', err));
    }

    // jalankan tiap 1 detik
    setInterval(pollLastScan, 1000);
});
</script>
@endpush
