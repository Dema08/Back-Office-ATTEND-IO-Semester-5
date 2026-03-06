<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pertemuan;
use App\Models\Kehadiran;

class KehadiranController extends Controller
{
    /**
     * Form kelola kehadiran untuk satu pertemuan.
     */
    public function edit(Pertemuan $pertemuan)
    {
        $kelas = $pertemuan->kelas;

        // Ambil semua mahasiswa yang terdaftar di kelas ini
        $mahasiswa = $kelas->mahasiswa()->orderBy('nim')->get();

        // Ambil data kehadiran yang sudah ada untuk pertemuan ini
        $kehadiran = Kehadiran::where('pertemuan_id', $pertemuan->id)->get()
            ->keyBy('mahasiswa_id'); // jadi array [mahasiswa_id => Kehadiran]

        return view('kehadiran.edit', compact('pertemuan', 'kelas', 'mahasiswa', 'kehadiran'));
    }

    /**
     * Simpan/update kehadiran mahasiswa untuk pertemuan ini.
     */
    public function update(Request $request, Pertemuan $pertemuan)
    {
        $kelas = $pertemuan->kelas;

        // Validasi struktur input (isi bisa null utk poin_fokus)
        $data = $request->validate([
            'kehadiran'                  => 'required|array',
            'kehadiran.*.status'         => 'required|in:hadir,tidak_hadir',
            'kehadiran.*.poin_fokus'     => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($data['kehadiran'] as $mahasiswaId => $row) {
            Kehadiran::updateOrCreate(
                [
                    'pertemuan_id' => $pertemuan->id,
                    'kelas_id'     => $kelas->id,
                    'mahasiswa_id' => $mahasiswaId,
                ],
                [
                    'status'       => $row['status'],
                    'poin_fokus'   => $row['poin_fokus'] ?? 0,
                ]
            );
        }

        return redirect()
            ->route('pertemuan.history')
            ->with('success', 'Data kehadiran pertemuan ID '.$pertemuan->id.' berhasil disimpan.');
    }
}
