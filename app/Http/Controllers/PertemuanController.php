<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\Kelas;
use App\Models\Pertemuan;
use App\Models\Kehadiran;
use Illuminate\Http\JsonResponse;

class PertemuanController extends Controller
{
    // ================= HALAMAN CLASS ASSIST =================
    public function create(Request $request)
    {
        // data dropdown
        $dosen      = Dosen::orderBy('nama_dosen')->get();
        $mataKuliah = MataKuliah::orderBy('nama_matkul')->get();
        $kelas      = Kelas::orderBy('golongan')->get();

        $pertemuan  = null;
        $kehadiran  = collect();

        // === AUTO-REDIRECT KE PERTEMUAN AKTIF JIKA TIDAK ADA pertemuan_id ===
        if (! $request->filled('pertemuan_id')) {
            $active = Pertemuan::where('status', 'ongoing')
                ->latest('id')
                ->first();

            if ($active) {
                return redirect()->route('class-assist', [
                    'pertemuan_id' => $active->id,
                ]);
            }
        }

        // === LOAD PERTEMUAN BERDASARKAN pertemuan_id (kalau ada) ===
        if ($request->filled('pertemuan_id')) {
            $pertemuan = Pertemuan::with([
                    'kelas.dosen',
                    'kelas.mataKuliah',
                    'kehadiran.mahasiswa',
                ])->find($request->pertemuan_id);

            if ($pertemuan) {
                // generate kehadiran kalau belum ada sama sekali
                if ($pertemuan->kehadiran()->count() === 0) {
                    $kelasWithMhs = $pertemuan->kelas()->with('mahasiswa')->first();

                    if ($kelasWithMhs) {
                        foreach ($kelasWithMhs->mahasiswa as $mhs) {
                            Kehadiran::firstOrCreate(
                                [
                                    'pertemuan_id' => $pertemuan->id,
                                    'mahasiswa_id' => $mhs->id,
                                ],
                                [
                                    'kelas_id'   => $pertemuan->kelas_id,
                                    'status'     => 0, // 0 = tidak hadir
                                    'poin_fokus' => 0,
                                ]
                            );
                        }

                        $pertemuan->load('kehadiran.mahasiswa');
                    }
                }

                // urutkan berdasarkan NIM
                $kehadiran = $pertemuan->kehadiran
                    ->sortBy(fn ($r) => $r->mahasiswa->nim ?? '')
                    ->values();
            }
        }

        return view('pertemuan.create', [
            'dosen'      => $dosen,
            'mataKuliah' => $mataKuliah,
            'kelas'      => $kelas,
            'pertemuan'  => $pertemuan,
            'kehadiran'  => $kehadiran,
        ]);
    }

    // ================= AJAX OPTIONS (DOSEN → MATA KULIAH → KELAS) =================
    public function options(Request $request)
    {
        $dosenId      = $request->query('dosen_id');
        $mataKuliahId = $request->query('mata_kuliah_id');

        $result = [
            'mata_kuliah' => [],
            'kelas'       => [],
        ];

        // STEP 1: dari dosen → list matkul yang diampu
        if ($dosenId) {
            $mkIds = Kelas::where('dosen_id', $dosenId)
                ->pluck('mata_kuliah_id')
                ->unique()
                ->filter()
                ->values()
                ->all();

            if (!empty($mkIds)) {
                $mkList = MataKuliah::whereIn('id', $mkIds)
                    ->orderBy('nama_matkul')
                    ->get(['id', 'nama_matkul', 'kode_matkul']);

                $result['mata_kuliah'] = $mkList->map(function ($mk) {
                    return [
                        'id'        => $mk->id,
                        'nama_full' => $mk->kode_matkul
                            ? "{$mk->nama_matkul} ({$mk->kode_matkul})"
                            : $mk->nama_matkul,
                    ];
                })->values()->all();
            }
        }

        // STEP 2: dosen + matkul → list kelas
        if ($dosenId && $mataKuliahId) {
            $kelasList = Kelas::where('dosen_id', $dosenId)
                ->where('mata_kuliah_id', $mataKuliahId)
                ->orderBy('golongan')
                ->get(['id', 'golongan']);

            $result['kelas'] = $kelasList->map(function ($k) {
                return [
                    'id'       => $k->id,
                    'golongan' => $k->golongan,
                ];
            })->values()->all();
        }

        return response()->json($result);
    }

    // ================= TOMBOL "MULAI PERTEMUAN" =================
    public function start(Request $request)
    {
        $validated = $request->validate([
            'dosen_id'       => 'required|exists:dosen,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'kelas_id'       => 'required|exists:kelas,id',
            'minggu_ke'      => 'required|integer|min:1',
            'acara_ke'       => 'required|integer|min:1',
            'tanggal'        => 'required|date',
            'jam_mulai'      => 'required',
        ]);

          // === CEK DULU: APAKAH MASIH ADA PERTEMUAN YANG ONGOING? ===
    // Jika ingin GLOBAL (semua kelas), pakai tanpa filter kelas_id:
    $ongoing = Pertemuan::where('status', 'ongoing')->first();

    // Kalau mau dibatasi per kelas, ganti dengan:
    // $ongoing = Pertemuan::where('status', 'ongoing')
    //     ->where('kelas_id', $validated['kelas_id'])
    //     ->first();

    if ($ongoing) {
        // Arahkan ke pertemuan yang masih berjalan + beri pesan error
        return redirect()
            ->route('class-assist', ['pertemuan_id' => $ongoing->id])
            ->with('error', 'Masih ada pertemuan yang sedang berjalan. Silakan akhiri pertemuan tersebut sebelum memulai pertemuan baru.');
    }

        // buat pertemuan baru
        $pertemuan = Pertemuan::create([
            'kelas_id'       => $validated['kelas_id'],
            'dosen_id'       => $validated['dosen_id'],
            'mata_kuliah_id' => $validated['mata_kuliah_id'],
            'minggu_ke'      => $validated['minggu_ke'],
            'acara_ke'       => $validated['acara_ke'],
            'tanggal'        => $validated['tanggal'],
            'jam_mulai'      => Carbon::now()->format('H:i:s'),
            'status'         => 'ongoing',
        ]);



        // generate kehadiran
        $kelas = Kelas::with('mahasiswa')->find($validated['kelas_id']);

        if ($kelas) {
            foreach ($kelas->mahasiswa as $mhs) {
                Kehadiran::firstOrCreate(
                    [
                        'pertemuan_id' => $pertemuan->id,
                        'mahasiswa_id' => $mhs->id,
                    ],
                    [
                        'kelas_id'   => $pertemuan->kelas_id,
                        'status'     => 0,
                        'poin_fokus' => 0,
                    ]
                );
            }
        }

        // ==== JALANKAN PYTHON DI BACKGROUND (WINDOWS) ====
        try {
            $python = 'C:\\ATTEND-IO\\venv310\\Scripts\\python.exe';
            $script = 'C:\\ATTEND-IO\\realtime_pcv_system.py';

            $pertemuanId = $pertemuan->id;
            $cmd = 'cmd /c start "" "' . $python . '" "' . $script . '" ' . $pertemuanId;

            pclose(popen($cmd, 'r'));
        } catch (\Throwable $e) {
            \Log::error('Gagal menjalankan Python realtime_pcv_system: '.$e->getMessage());
        }

        return redirect()
            ->route('class-assist', ['pertemuan_id' => $pertemuan->id]);    }

    // ================= TOMBOL "AKHIRI PERTEMUAN" =================
    public function end(Request $request)
    {
        $data = $request->validate([
            'pertemuan_id' => 'required|exists:pertemuan,id',
        ]);

        $pertemuan = Pertemuan::findOrFail($data['pertemuan_id']);
        $pertemuan->jam_selesai = now()->format('H:i:s');
        $pertemuan->status      = 'finished';
        $pertemuan->save();

        return redirect()
            ->route('class-assist')
            ->with('success', 'Pertemuan telah diakhiri. Data kehadiran & poin fokus tersimpan.');
    }

    // ================= JSON UNTUK AUTO-REFRESH KEHADIRAN =================
    public function kehadiranData(Pertemuan $pertemuan): JsonResponse
    {
        $rows = $pertemuan->kehadiran()
            ->with('mahasiswa')
            ->get()
            ->sortBy(fn ($r) => $r->mahasiswa->nim ?? '')
            ->values()
            ->map(function ($kh, $idx) {
                return [
                    'no'         => $idx + 1,
                    'nim'        => $kh->mahasiswa->nim ?? '-',
                    'nama'       => $kh->mahasiswa->nama ?? '-',
                    'status'     => $kh->status == 1 ? 'Hadir' : 'Tidak hadir',
                    'poin_fokus' => $kh->poin_fokus,
                ];
            });

        return response()->json($rows);
    }
}
