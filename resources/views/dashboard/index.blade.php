@extends('layouts.main')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
    <div class="space-y-6">

        {{-- STAT CARD --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Total Dosen --}}
            <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 p-4">
                <p class="text-xs text-slate-400 mb-1">Total Dosen</p>
                <p class="text-3xl font-semibold text-indigo-400">{{ $totalDosen }}</p>
            </div>

            {{-- Total Mahasiswa --}}
            <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 p-4">
                <p class="text-xs text-slate-400 mb-1">Total Mahasiswa</p>
                <p class="text-3xl font-semibold text-emerald-400">{{ $totalMahasiswa }}</p>
            </div>

            {{-- Total Kelas --}}
            <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 p-4">
                <p class="text-xs text-slate-400 mb-1">Total Kelas</p>
                <p class="text-3xl font-semibold text-fuchsia-400">{{ $totalKelas }}</p>
            </div>

            {{-- Pertemuan minggu ini --}}
            <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80 p-4">
                <p class="text-xs text-slate-400 mb-1">Pertemuan Minggu Ini</p>
                <p class="text-3xl font-semibold text-amber-400">{{ $pertemuanMingguIni }}</p>
            </div>
        </div>

        {{-- CHART + TABEL TERBARU --}}
        <div class="grid gap-6 lg:grid-cols-5">
            {{-- GRAFIK --}}
            <div class="lg:col-span-3 rounded-2xl bg-slate-900/70 border border-slate-800/80 p-4">
                <h2 class="text-lg font-semibold mb-3">Grafik Jumlah Pertemuan 7 Hari Terakhir</h2>
                <div class="h-64">
                    <canvas id="pertemuanChart"></canvas>
                </div>
            </div>

            {{-- TABEL PERTEMUAN TERBARU --}}
            <div class="lg:col-span-2 rounded-2xl bg-slate-900/70 border border-slate-800/80 p-4">
                <h2 class="text-lg font-semibold mb-3">Pertemuan Terbaru</h2>

                @if ($pertemuanTerbaru->isEmpty())
                    <p class="text-sm text-slate-400">
                        Belum ada pertemuan yang tercatat.
                    </p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs">
                            <thead class="text-slate-400 border-b border-slate-800/80">
                                <tr>
                                    <th class="py-2 px-3 text-left">Tanggal</th>
                                    <th class="py-2 px-3 text-left">Jam</th>
                                    <th class="py-2 px-3 text-left">Kelas</th>
                                    <th class="py-2 px-3 text-left">Mata Kuliah</th>
                                    <th class="py-2 px-3 text-left">Dosen</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/80 text-slate-200">
                                @foreach ($pertemuanTerbaru as $p)
                                    <tr>
                                        <td class="py-2 px-3">
                                            {{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}
                                        </td>
                                        <td class="py-2 px-3">
                                            {{ $p->jam_mulai }}{{ $p->jam_selesai ? ' - '.$p->jam_selesai : '' }}
                                        </td>
                                        <td class="py-2 px-3">
                                            {{ optional($p->kelas)->golongan ?? '-' }}
                                        </td>
                                        <td class="py-2 px-3">
                                            {{ optional($p->mataKuliah)->nama_matkul ?? '-' }}
                                        </td>
                                        <td class="py-2 px-3">
                                            {{ optional($p->dosen)->nama_dosen ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('pertemuanChart').getContext('2d');

        const chartLabels = @json($chartLabels);
        const chartData   = @json($chartData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Jumlah Pertemuan',
                    data: chartData,
                    borderWidth: 2,
                    tension: 0.3,
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#e5e7eb',
                            font: { size: 11 }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#9ca3af', font: { size: 10 } },
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#9ca3af',
                            font: { size: 10 },
                            precision: 0
                        },
                        grid: { color: 'rgba(55,65,81,0.4)' }
                    }
                }
            }
        });
    </script>
@endpush
