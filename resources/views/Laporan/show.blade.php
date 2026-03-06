@extends('layouts.main')

@section('title', 'Detail Pertemuan')
@section('page_title', 'Detail Pertemuan')

@section('content')
    <div class="space-y-6">

        {{-- INFO PERTEMUAN --}}
        <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 p-4">
            <h2 class="text-lg font-semibold mb-1">
                Detail Pertemuan
            </h2>

            <p class="text-xs text-slate-400">
                Tanggal:
                <span class="text-slate-200">{{ $pertemuan->tanggal }}</span> <br>
                Jam:
                <span class="text-slate-200">
                    {{ $pertemuan->jam_mulai }}{{ $pertemuan->jam_selesai ? ' - ' . $pertemuan->jam_selesai : '' }}
                </span> <br>
                Dosen:
                <span class="text-slate-200">
                    {{ $pertemuan->kelas->dosen->nama_dosen ?? '-' }}
                </span> <br>
                Mata Kuliah:
                <span class="text-slate-200">
                    {{ $pertemuan->kelas->mataKuliah->nama_matkul ?? '-' }}
                </span> <br>
                Golongan:
                <span class="text-slate-200">{{ $pertemuan->kelas->golongan ?? '-' }}</span> <br>
                Minggu ke:
                <span class="text-slate-200">{{ $pertemuan->minggu_ke }}</span>,
                Acara ke:
                <span class="text-slate-200">{{ $pertemuan->acara_ke }}</span>
            </p>

            <div class="mt-3">
                <a href="{{ route('laporan.index', request()->query()) }}"
                   class="inline-flex items-center rounded-xl bg-slate-800 px-3 py-1.5 text-[11px] font-semibold text-slate-100">
                    ← Kembali ke Laporan
                </a>
            </div>
        </div>

        {{-- TABEL KEHADIRAN MAHASISWA --}}
        <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 p-4">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-lg font-semibold">Kehadiran Mahasiswa</h2>
                    <span class="text-xs text-slate-400">
                        {{ $pertemuan->kelas->mataKuliah->nama_matkul ?? '-' }}
                        • {{ $pertemuan->kelas->golongan ?? '-' }}
                        • Minggu ke-{{ $pertemuan->minggu_ke }}, Acara ke-{{ $pertemuan->acara_ke }}
                    </span>
                </div>
            </div>

            @if ($kehadiran->isEmpty())
                <p class="text-sm text-slate-400">
                    Belum ada data kehadiran untuk pertemuan ini.
                </p>
            @else
                <div class="overflow-x-auto mt-2">
                    <table class="min-w-full text-xs">
                        <thead class="text-slate-400 border-b border-slate-800/80">
                            <tr>
                                <th class="py-2 px-3 text-left">No</th>
                                <th class="py-2 px-3 text-left">NIM</th>
                                <th class="py-2 px-3 text-left">Nama</th>
                                <th class="py-2 px-3 text-left">Status</th>
                                <th class="py-2 px-3 text-left">Poin Fokus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80 text-slate-200">
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
            @endif
        </div>
    </div>
@endsection

