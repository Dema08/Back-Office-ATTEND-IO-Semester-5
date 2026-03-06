@extends('layouts.main')

@section('title', 'Laporan')
@section('page_title', 'Laporan Pertemuan')

@section('content')
    <div class="space-y-6">
        {{-- FILTER --}}
        <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 p-4">
            <h2 class="text-lg font-semibold mb-3">Filter Laporan</h2>

            <form action="{{ route('laporan.index') }}" method="GET"
                  class="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
                {{-- Dosen --}}
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Dosen</label>
                    <select name="dosen_id" id="filter-dosen"
                            class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm">
                        <option value="">-- Pilih Dosen --</option>
                        @foreach ($dosen as $d)
                            <option value="{{ $d->id }}" @selected(($filters['dosen_id'] ?? '') == $d->id)>
                                {{ $d->nama_dosen }} ({{ $d->nip }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Mata Kuliah (DIISI JS, BUKAN SERVER) --}}
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Mata Kuliah</label>
                    <select name="mata_kuliah_id" id="filter-matkul"
                            class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm">
                        {{-- ⛔️ SENGAJA dikosongkan, hanya placeholder --}}
                        <option value="">-- Pilih Mata Kuliah --</option>
                    </select>
                    <p class="mt-1 text-[10px] text-slate-500" id="hint-matkul">
                        *Pilih dosen untuk memfilter daftar mata kuliah.
                    </p>
                </div>

                {{-- Golongan --}}
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Golongan Kelas</label>
                    <select name="golongan"
                            class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm">
                        <option value="">-- Pilih Golongan --</option>
                        @foreach ($golongan as $g)
                            <option value="{{ $g->golongan }}" @selected(($filters['golongan'] ?? '') == $g->golongan)>
                                {{ $g->golongan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Minggu & Acara (opsional) --}}
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Minggu ke- (opsional)</label>
                    <input type="number" name="minggu_ke" min="1"
                           value="{{ $filters['minggu_ke'] ?? '' }}"
                           class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-xs text-slate-400 mb-1">Acara ke- (opsional)</label>
                    <input type="number" name="acara_ke" min="1"
                           value="{{ $filters['acara_ke'] ?? '' }}"
                           class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="w-full rounded-xl bg-indigo-500 px-4 py-2 text-sm font-semibold text-white">
                        Tampilkan
                    </button>
                </div>
            </form>
        </div>

        {{-- HISTORY PERTEMUAN --}}
        <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 p-4">
            <div class="flex items-center justify-between mb-3 gap-2">
                <h2 class="text-lg font-semibold">Riwayat Pertemuan</h2>

                @if (! $riwayatPertemuan->isEmpty())
                    <a href="{{ route('laporan.export', $filters) }}"
                       class="inline-flex items-center rounded-xl bg-emerald-500 px-3 py-1.5 text-xs font-semibold text-white shadow shadow-emerald-500/30">
                        ⬇️ Export Excel
                    </a>
                @endif
            </div>

            @if ($riwayatPertemuan->isEmpty())
                <p class="text-sm text-slate-400">
                    Belum ada pertemuan yang cocok dengan filter,
                    atau Anda belum menjalankan filter.
                </p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs">
                        <thead class="text-slate-400 border-b border-slate-800/80">
                            <tr>
                                <th class="py-2 px-3 text-left">Tanggal</th>
                                <th class="py-2 px-3 text-left">Jam</th>
                                <th class="py-2 px-3 text-left">Dosen</th>
                                <th class="py-2 px-3 text-left">Mata Kuliah</th>
                                <th class="py-2 px-3 text-left">Golongan</th>
                                <th class="py-2 px-3 text-left">Minggu</th>
                                <th class="py-2 px-3 text-left">Acara</th>
                                <th class="py-2 px-3 text-left">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80 text-slate-200">
                            @foreach ($riwayatPertemuan as $p)
                                <tr>
                                    <td class="py-2 px-3">{{ $p->tanggal }}</td>
                                    <td class="py-2 px-3">
                                        {{ $p->jam_mulai }}{{ $p->jam_selesai ? ' - ' . $p->jam_selesai : '' }}
                                    </td>
                                    <td class="py-2 px-3">
                                        {{ $p->kelas->dosen->nama_dosen ?? '-' }}
                                    </td>
                                    <td class="py-2 px-3">
                                        {{ $p->kelas->mataKuliah->nama_matkul ?? '-' }}
                                    </td>
                                    <td class="py-2 px-3">
                                        {{ $p->kelas->golongan ?? '-' }}
                                    </td>
                                    <td class="py-2 px-3">{{ $p->minggu_ke }}</td>
                                    <td class="py-2 px-3">{{ $p->acara_ke }}</td>

                                    {{-- TOMBOL DETAIL (bawa filter di query) --}}
                                    <td class="py-2 px-3">
                                        <a href="{{ route('laporan.show', ['pertemuan' => $p->id] + $filters) }}"
                                           class="inline-flex items-center rounded-xl bg-indigo-500/90 hover:bg-indigo-500 px-3 py-1.5 text-[11px] font-semibold text-white">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- JS: isi dropdown matkul pakai endpoint /pertemuan/options --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dosenSelect   = document.getElementById('filter-dosen');
            const matkulSelect  = document.getElementById('filter-matkul');
            const hintMatkul    = document.getElementById('hint-matkul');
            const currentMatkul = @json($filters['mata_kuliah_id'] ?? null);

            function resetMatkul() {
                matkulSelect.innerHTML = '<option value="">-- Pilih Mata Kuliah --</option>';
            }

            function loadMatkul(dosenId) {
                resetMatkul();

                if (!dosenId) {
                    if (hintMatkul) {
                        hintMatkul.textContent = '*Pilih dosen untuk memfilter daftar mata kuliah.';
                    }
                    return;
                }

                const url = "{{ route('pertemuan.options') }}" + '?dosen_id=' + encodeURIComponent(dosenId);

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        const list = data.mata_kuliah || [];

                        list.forEach(function (mk) {
                            const opt = document.createElement('option');
                            opt.value = mk.id;
                            opt.textContent = mk.nama_full; // sesuai dengan PertemuanController::options
                            if (String(mk.id) === String(currentMatkul)) {
                                opt.selected = true;
                            }
                            matkulSelect.appendChild(opt);
                        });

                        if (hintMatkul) {
                            hintMatkul.textContent = '*Daftar mata kuliah difilter berdasarkan dosen terpilih.';
                        }
                    })
                    .catch(err => {
                        console.error('Gagal ambil mata kuliah:', err);
                        if (hintMatkul) {
                            hintMatkul.textContent = '*Gagal memuat mata kuliah. Coba reload halaman.';
                        }
                    });
            }

            // ketika dosen berubah → load matkul yg diampu
            dosenSelect.addEventListener('change', function () {
                loadMatkul(this.value);
            });

            // kalau halaman dibuka dan dosen sudah terpilih (setelah submit filter)
            if (dosenSelect.value) {
                loadMatkul(dosenSelect.value);
            }
        });
    </script>
@endsection
