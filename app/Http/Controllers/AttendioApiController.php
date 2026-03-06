<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kehadiran;
use App\Models\Mahasiswa;
use App\Models\Pertemuan;
use App\Models\RfidTag;
use Illuminate\Support\Facades\Cache;

class AttendioApiController extends Controller
{
    public function storeFocusEvent(Request $request)
    {

        $data = $request->validate([
            'pertemuan_id' => 'required|integer',
            'nama'         => 'required|string',
            'poin'         => 'required|integer',
            'focus_status' => 'nullable|string',
        ]);

        // 1) cari mahasiswa dari nama
        $mahasiswa = Mahasiswa::where('nama', $data['nama'])->first();

        if (!$mahasiswa) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Mahasiswa tidak ditemukan: ' . $data['nama'],
            ], 404);
        }

        // 2) cari kehadiran untuk pertemuan + mahasiswa tsb
        $kehadiran = Kehadiran::where('pertemuan_id', $data['pertemuan_id'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        if (!$kehadiran) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kehadiran tidak ditemukan untuk pertemuan ini',
            ], 404);
        }

        // 3) tambahkan poin_fokus
        $kehadiran->increment('poin_fokus', $data['poin']);

        // Opsional: kalau dia tadinya status=0 (tidak hadir), anggap hadir ketika sudah ada event fokus
        if ($kehadiran->status == 0) {
            $kehadiran->status = 1;
            $kehadiran->save();
        }

        return response()->json([
            'status'       => 'ok',
            'message'      => 'Poin fokus/tidak fokus tercatat',
            'nama'         => $mahasiswa->nama,
            'pertemuan_id' => $data['pertemuan_id'],
            'poin_ditambah'=> $data['poin'],
            'poin_total'   => $kehadiran->poin_fokus,
        ]);
    }

    public function storeRfidAttendance(Request $request)
    {
        // Kontrak:
        // pertemuan_id : ID pertemuan aktif
        // nim          : NIM mahasiswa
        // status       : 1 = hadir (0 optional)

        $data = $request->validate([
            'pertemuan_id' => 'required|integer',
            'nim'          => 'required|string',
            'status'       => 'required|integer|in:0,1',
        ]);

        // 1) Cari mahasiswa berdasarkan NIM
        $mahasiswa = Mahasiswa::where('nim', $data['nim'])->first();

        if (!$mahasiswa) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Mahasiswa dengan NIM '.$data['nim'].' tidak ditemukan',
            ], 404);
        }

        // 2) Cari baris kehadiran untuk pertemuan + mahasiswa tsb
        $kehadiran = Kehadiran::where('pertemuan_id', $data['pertemuan_id'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        if (!$kehadiran) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kehadiran tidak ditemukan untuk pertemuan ini',
            ], 404);
        }

        // 3) Update status hadir
        if ($data['status'] == 1 && $kehadiran->status != 1) {
            $kehadiran->status = 1;   // 1 = hadir
            $kehadiran->save();
        }

        return response()->json([
            'status'       => 'ok',
            'message'      => 'Status kehadiran diperbarui',
            'pertemuan_id' => $data['pertemuan_id'],
            'nim'          => $mahasiswa->nim,
            'nama'         => $mahasiswa->nama,
            'status_hadir' => $kehadiran->status,
        ]);
    }
    public function pertemuanStatus(Pertemuan $pertemuan)
{
    return response()->json([
        'id'          => $pertemuan->id,
        'status'      => $pertemuan->status,   
        'jam_mulai'   => $pertemuan->jam_mulai,
        'jam_selesai' => $pertemuan->jam_selesai,
    ]);
}
public function getActivePertemuan()
    {
        // ambil pertemuan dengan status 'ongoing' paling baru
        $p = Pertemuan::where('status', 'ongoing')
            ->latest('id')
            ->first();

        if (! $p) {
            return response()->json([
                'pertemuan_id' => null,
                'status'       => 'none',
            ]);
        }

        return response()->json([
            'pertemuan_id' => $p->id,
            'kelas_id'     => $p->kelas_id,
            'status'       => $p->status,
        ]);
    }

    public function rfidLookup(Request $request)
    {
        $raw = $request->query('uid', '');

        $uid = strtoupper(preg_replace('/[^0-9A-Fa-f]/', '', $raw));

        if (!$uid) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Parameter uid wajib diisi',
            ], 400);
        }

        $tag = RfidTag::with('mahasiswa')
            ->where('uid_hex', $uid)
            ->first();

        if (! $tag) {
            return response()->json([
                'status' => 'not_found',
                'uid_hex' => $uid,
            ], 404);
        }

        return response()->json([
            'status'        => 'ok',
            'uid_hex'       => $uid,
            'mahasiswa_id'  => $tag->mahasiswa_id,
            'nim'           => $tag->mahasiswa->nim,
            'nama'          => $tag->mahasiswa->nama,
            'golongan'      => $tag->mahasiswa->golongan,
        ]);
    }

    // ========== 2) SCAN: ESP kirim UID terakhir (untuk form register) ==========
    public function rfidScan(Request $request)
    {
        $raw = $request->input('uid_hex', '');
        $uid = strtoupper(preg_replace('/[^0-9A-Fa-f]/', '', $raw));

        if (!$uid) {
            return response()->json([
                'status'  => 'error',
                'message' => 'uid_hex wajib diisi',
            ], 400);
        }

        // Simpan di cache 30 detik sebagai "last scanned uid"
        Cache::put('rfid:last_scan', $uid, now()->addSeconds(30));

        $tag = RfidTag::with('mahasiswa')
            ->where('uid_hex', $uid)
            ->first();

        return response()->json([
            'status'     => 'ok',
            'uid_hex'    => $uid,
            'registered' => (bool) $tag,
            'mahasiswa'  => $tag ? [
                'id'   => $tag->mahasiswa_id,
                'nim'  => $tag->mahasiswa->nim,
                'nama' => $tag->mahasiswa->nama,
            ] : null,
        ]);
    }

    // ========== 3) GET LAST SCAN: dipolling oleh Blade ==========
    public function rfidLastScan()
    {
        $uid = Cache::get('rfid:last_scan');

        if (!$uid) {
            return response()->json([
                'status' => 'empty',
            ]);
        }

        $tag = RfidTag::with('mahasiswa')
            ->where('uid_hex', $uid)
            ->first();

        return response()->json([
            'status'     => 'ok',
            'uid_hex'    => $uid,
            'registered' => (bool) $tag,
            'mahasiswa'  => $tag ? [
                'id'   => $tag->mahasiswa_id,
                'nim'  => $tag->mahasiswa->nim,
                'nama' => $tag->mahasiswa->nama,
            ] : null,
        ]);
    }

    public function rfidMode(Request $request)
    {
        // default: attendance
        $mode = Cache::get('rfid_mode', 'attendance');

        return response()->json([
            'mode' => $mode,
        ]);
    }
    public function setRfidMode(Request $request)
    {
        $mode = $request->input('mode');

        if (! in_array($mode, ['attendance', 'register'], true)) {
            return response()->json([
                'message' => 'Mode tidak valid. Gunakan "attendance" atau "register".',
            ], 422);
        }

        Cache::forever('rfid_mode', $mode);

        return response()->json([
            'mode'    => $mode,
            'message' => 'Mode RFID diperbarui.',
        ]);
    }

}

