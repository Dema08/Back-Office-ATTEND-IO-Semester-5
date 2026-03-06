@extends('layouts.main')

@section('title', 'Class Assist')
@section('page_title', 'Class Assist')

@section('content')


    {{-- banner permanen selama pertemuan masih ongoing --}}
    @if (!empty($pertemuan) && $pertemuan->status === 'ongoing')
        <div class="mb-4 rounded-lg bg-emerald-500/10 border border-emerald-500/40 px-4 py-2 text-sm text-emerald-300">
            Pertemuan dimulai. Sistem AI &amp; kehadiran berjalan.
        </div>
    @endif

        {{-- flash success dari start / end pertemuan --}}
    @if (session('success'))
        <div class="mb-3 rounded-lg bg-emerald-500/10 border border-emerald-500/40 px-4 py-2 text-sm text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    {{-- flash error kalau masih ada pertemuan ongoing --}}
    @if (session('error'))
        <div class="mb-3 rounded-lg bg-rose-500/10 border border-rose-500/40 px-4 py-2 text-sm text-rose-200">
            {{ session('error') }}
        </div>
    @endif


    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-rose-500/10 border border-rose-500/40 px-4 py-2 text-xs text-rose-200">
            @foreach ($errors->all() as $err)
                <p>{{ $err }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- FORM MULAI PERTEMUAN --}}
        <div class="lg:col-span-1 rounded-2xl bg-slate-900/70 border border-slate-800/80 p-4 space-y-3">
            <h2 class="text-lg font-semibold mb-2">Mulai Pertemuan</h2>

            <form action="{{ route('class-assist.start') }}" method="POST" class="space-y-3">
                @csrf

                {{-- Dosen --}}
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Dosen</label>
                    <select id="dosen_select" name="dosen_id" required
                            class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm">
                        <option value="">-- Pilih Dosen --</option>
                        @foreach ($dosen as $d)
                            <option value="{{ $d->id }}" @selected(old('dosen_id') == $d->id)>
                                {{ $d->nama_dosen }}{{ $d->nip ? ' ('.$d->nip.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('dosen_id')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Mata Kuliah --}}
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Mata Kuliah</label>
                    <select id="mk_select" name="mata_kuliah_id" required
                            class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm">
                        <option value="">-- Pilih Mata Kuliah --</option>
                    </select>
                    @error('mata_kuliah_id')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kelas --}}
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Kelas / Golongan</label>
                    <select id="kelas_select" name="kelas_id" required
                            class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm">
                        <option value="">-- Pilih Kelas --</option>
                    </select>
                    @error('kelas_id')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Minggu ke & Acara ke --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Minggu ke-</label>
                        <input type="number" min="1" name="minggu_ke" required
                               value="{{ old('minggu_ke', 1) }}"
                               class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Acara ke-</label>
                        <input type="number" min="1" name="acara_ke" required
                               value="{{ old('acara_ke', 1) }}"
                               class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm">
                    </div>
                </div>

                {{-- Tanggal & Jam --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Tanggal</label>
                        <input type="date" name="tanggal" required
                               value="{{ old('tanggal', now()->toDateString()) }}"
                               class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Jam Mulai</label>
                        <input type="time" name="jam_mulai" required
                               value="{{ old('jam_mulai', now()->format('H:i')) }}"
                               class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm">
                    </div>
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-indigo-500 px-4 py-2 text-sm font-semibold text-white mt-2">
                    Mulai Pertemuan
                </button>
            </form>
        </div>

        {{-- TABEL KEHADIRAN --}}
        @if ($pertemuan)
            <div class="lg:col-span-2 rounded-2xl bg-slate-900/70 border border-slate-800/80 p-4">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-semibold">
                            Kehadiran Mahasiswa
                        </h2>
                        <span class="text-xs text-slate-400">
                            {{ $pertemuan->mataKuliah->nama_matkul ?? ($pertemuan->kelas->mataKuliah->nama_matkul ?? '-') }}
                            • {{ $pertemuan->kelas->golongan ?? '-' }} •
                            Minggu ke-{{ $pertemuan->minggu_ke }}, Acara ke-{{ $pertemuan->acara_ke }}
                        </span>
                    </div>

                    {{-- Tombol Akhiri Pertemuan --}}
                    @if ($pertemuan->status === 'ongoing')
                        <form action="{{ route('class-assist.end') }}" method="POST"
                              onsubmit="return confirm('Akhiri pertemuan ini? AI & kehadiran akan dihentikan.');">
                            @csrf
                            <input type="hidden" name="pertemuan_id" value="{{ $pertemuan->id }}">
                            <button type="submit"
                                    class="rounded-xl bg-rose-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-600">
                                Akhiri Pertemuan
                            </button>
                        </form>
                    @else
                        <span class="text-xs px-2 py-1 rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/40">
                            Selesai
                        </span>
                    @endif
                </div>

                <div class="overflow-x-auto mt-3">
                    <table class="min-w-full text-xs">
                        <thead class="text-slate-400 border-b border-slate-800/80">
                            <tr>
                                <th class="py-2 px-3 text-left">No</th>
                                <th class="py-2 px-3 text-left">NIM</th>
                                <th class="py-2 px-3 text-left">Nama</th>
                                <th class="py-2 px-3 text-left">Status Kehadiran</th>
                                <th class="py-2 px-3 text-left">Poin Tidak Fokus</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-kehadiran-body" class="divide-y divide-slate-800/80 text-slate-200">
                            @foreach ($kehadiran as $idx => $kh)
                                <tr>
                                    <td class="py-2 px-3">{{ $idx + 1 }}</td>
                                    <td class="py-2 px-3">{{ $kh->mahasiswa->nim ?? '-' }}</td>
                                    <td class="py-2 px-3">{{ $kh->mahasiswa->nama ?? '-' }}</td>
                                    <td class="py-2 px-3">
                                        {{ $kh->status == 1 ? 'Hadir' : 'Tidak hadir' }}
                                    </td>
                                    <td class="py-2 px-3">{{ $kh->poin_fokus }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="lg:col-span-2 rounded-2xl bg-slate-900/70 border border-slate-800/80 p-4">
                <p class="text-sm text-slate-400">
                    Belum ada pertemuan aktif. Silakan isi form di sebelah kiri dan klik
                    <strong>Mulai Pertemuan</strong>.
                </p>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const dosenSelect = document.getElementById('dosen_select');
    const mkSelect    = document.getElementById('mk_select');
    const kelasSelect = document.getElementById('kelas_select');

    if (!dosenSelect || !mkSelect || !kelasSelect) return;

    function resetSelect(select, placeholder) {
        select.innerHTML = '';
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = placeholder;
        select.appendChild(opt);
    }

    function loadOptions(params = {}) {
        const url = new URL("{{ route('pertemuan.options') }}", window.location.origin);
        Object.entries(params).forEach(([k, v]) => url.searchParams.append(k, v));
        return fetch(url).then(res => res.json());
    }

    // DOSEN → MATA KULIAH
    dosenSelect.addEventListener('change', () => {
        const dosenId = dosenSelect.value;

        resetSelect(mkSelect, '-- Pilih Mata Kuliah --');
        resetSelect(kelasSelect, '-- Pilih Kelas --');

        if (!dosenId) return;

        loadOptions({ dosen_id: dosenId })
            .then(data => {
                (data.mata_kuliah || []).forEach(mk => {
                    const opt = document.createElement('option');
                    opt.value = mk.id;
                    opt.textContent = mk.nama_full;
                    mkSelect.appendChild(opt);
                });
            })
            .catch(err => console.error(err));
    });

    // MATA KULIAH → KELAS
    mkSelect.addEventListener('change', () => {
        const dosenId = dosenSelect.value;
        const mkId    = mkSelect.value;

        resetSelect(kelasSelect, '-- Pilih Kelas --');

        if (!dosenId || !mkId) return;

        loadOptions({ dosen_id: dosenId, mata_kuliah_id: mkId })
            .then(data => {
                (data.kelas || []).forEach(k => {
                    const opt = document.createElement('option');
                    opt.value = k.id;
                    opt.textContent = k.golongan;
                    kelasSelect.appendChild(opt);
                });
            })
            .catch(err => console.error(err));
    });

    // ===== AUTO-REFRESH TABEL KEHADIRAN =====
    @if (!empty($pertemuan))
        const tbody = document.getElementById('tabel-kehadiran-body');

        function refreshKehadiran() {
            if (!tbody) return;

            fetch("{{ route('class-assist.kehadiran', ['pertemuan' => $pertemuan->id]) }}")
                .then(res => res.json())
                .then(rows => {
                    tbody.innerHTML = '';
                    rows.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="py-2 px-3">${row.no}</td>
                            <td class="py-2 px-3">${row.nim}</td>
                            <td class="py-2 px-3">${row.nama}</td>
                            <td class="py-2 px-3">${row.status}</td>
                            <td class="py-2 px-3">${row.poin_fokus}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(err => console.error('Gagal refresh kehadiran:', err));
        }

        setInterval(refreshKehadiran, 5000);
    @endif
});
</script>
@endpush
