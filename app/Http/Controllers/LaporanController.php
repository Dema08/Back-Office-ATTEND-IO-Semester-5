<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\Kelas;
use App\Models\Pertemuan;
use App\Exports\PertemuanDetailExport;
use Maatwebsite\Excel\Facades\Excel;


class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // data untuk dropdown
        $dosen      = Dosen::orderBy('nama_dosen')->get();
        $mataKuliah = MataKuliah::orderBy('nama_matkul')->get();
        $golongan   = Kelas::select('golongan')->distinct()->orderBy('golongan')->get();

        // ambil semua filter dari request
        $filters = $request->only([
            'dosen_id',
            'mata_kuliah_id',
            'golongan',
            'minggu_ke',
            'acara_ke',
        ]);

        // default: tidak ada riwayat dulu
        $riwayatPertemuan = collect();

        // cek apakah ADA minimal satu filter yang diisi
        $adaFilter = collect($filters)->filter(function ($val) {
            return $val !== null && $val !== '';
        })->isNotEmpty();

        if ($adaFilter) {
            $query = Pertemuan::with(['kelas.dosen', 'kelas.mataKuliah'])
                ->orderByDesc('tanggal')
                ->orderByDesc('jam_mulai');

            // filter yang terkait kelas (dosen, mk, golongan)
            $query->whereHas('kelas', function ($q) use ($filters) {
                if (!empty($filters['dosen_id'])) {
                    $q->where('dosen_id', $filters['dosen_id']);
                }
                if (!empty($filters['mata_kuliah_id'])) {
                    $q->where('mata_kuliah_id', $filters['mata_kuliah_id']);
                }
                if (!empty($filters['golongan'])) {
                    $q->where('golongan', $filters['golongan']);
                }
            });

            // filter minggu & acara (opsional)
            if (!empty($filters['minggu_ke'])) {
                $query->where('minggu_ke', $filters['minggu_ke']);
            }

            if (!empty($filters['acara_ke'])) {
                $query->where('acara_ke', $filters['acara_ke']);
            }

            $riwayatPertemuan = $query->get();
        }

        return view('laporan.index', [
            'dosen'            => $dosen,
            'mataKuliah'       => $mataKuliah,
            'golongan'         => $golongan,
            'filters'          => $filters,
            'riwayatPertemuan' => $riwayatPertemuan,
        ]);
    }

    public function show(Pertemuan $pertemuan)
    {
        // load relasi yang dibutuhkan
        $pertemuan->load([
            'kelas.dosen',
            'kelas.mataKuliah',
            'kehadiran.mahasiswa',
        ]);

        // urutkan kehadiran berdasarkan NIM
        $kehadiran = $pertemuan->kehadiran
            ->sortBy(fn ($r) => $r->mahasiswa->nim ?? '')
            ->values();

        return view('laporan.show', [
            'pertemuan' => $pertemuan,
            'kehadiran' => $kehadiran,
        ]);
    }

    public function export(Request $request)
{
    // ambil filter yang sama dengan yang dipakai di index()
    $filters = $request->only([
        'dosen_id',
        'mata_kuliah_id',
        'golongan',
        'minggu_ke',
        'acara_ke',
    ]);

    $fileName = 'laporan_pertemuan_' . now()->format('Ymd_His') . '.xlsx';

    return Excel::download(new PertemuanDetailExport($filters), $fileName);
}


}
