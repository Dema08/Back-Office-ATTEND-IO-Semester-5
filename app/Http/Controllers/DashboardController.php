<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Kelas;
use App\Models\Pertemuan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Card statistik
        $totalDosen      = Dosen::count();
        $totalMahasiswa  = Mahasiswa::count();
        $totalKelas = Kelas::select('golongan')
            ->distinct()
            ->count('golongan');   // hitung golongan unik


        $pertemuanMingguIni = Pertemuan::whereBetween('tanggal', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])->count();

        // Data grafik 7 hari terakhir
        $chartLabels = [];
        $chartData   = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d/m');
            $chartData[]   = Pertemuan::whereDate('tanggal', $date)->count();
        }

        // 5 pertemuan terbaru: include relasi kelas, mataKuliah, dan dosen
        $pertemuanTerbaru = Pertemuan::with(['kelas', 'mataKuliah', 'dosen'])
            ->orderByDesc('tanggal')
            ->orderByDesc('jam_mulai')
            ->limit(5)
            ->get();

        return view('dashboard.index', [
            'totalDosen'         => $totalDosen,
            'totalMahasiswa'     => $totalMahasiswa,
            'totalKelas'         => $totalKelas,
            'pertemuanMingguIni' => $pertemuanMingguIni,
            'chartLabels'        => $chartLabels,
            'chartData'          => $chartData,
            'pertemuanTerbaru'   => $pertemuanTerbaru,
        ]);
    }
}
