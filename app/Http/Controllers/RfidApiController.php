<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RfidScan;
use App\Models\RfidCard;   
use Illuminate\Support\Str;

class RfidApiController extends Controller
{
    /**
     * Dipanggil dari ESP32 setiap kali ada kartu discan.
     * Body: { "uid": "7D 25 29 02" } atau "7d252902"
     */
    public function storeScan(Request $request)
    {
        $request->validate([
            'uid' => 'required|string',
        ]);

        // Normalisasi UID: ambil hanya 0-9A-F, hapus spasi, uppercase
        $raw = strtoupper($request->input('uid'));
        $uidHex = preg_replace('/[^0-9A-F]/', '', $raw);

        if ($uidHex === '') {
            return response()->json([
                'status'  => 'error',
                'message' => 'UID tidak valid',
            ], 422);
        }

        // Simpan scan terbaru
        $scan = RfidScan::create([
            'uid_hex' => $uidHex,
        ]);

        // Cek apakah UID sudah terdaftar di tabel RfidCard
        $binding = RfidCard::with('mahasiswa')->where('uid_hex', $uidHex)->first();

        return response()->json([
            'status'      => 'ok',
            'uid_hex'     => $uidHex,
            'uid_pretty'  => implode(' ', str_split($uidHex, 2)), // "7D25..." -> "7D 25 ..."
            'registered'  => (bool) $binding,
            'mahasiswa'   => $binding && $binding->mahasiswa ? [
                'id'   => $binding->mahasiswa->id,
                'nim'  => $binding->mahasiswa->nim,
                'nama' => $binding->mahasiswa->nama,
            ] : null,
            'scan_id'     => $scan->id,
            'scanned_at'  => $scan->created_at,
        ]);
    }

    /**
     * Dipanggil dari halaman web (AJAX) untuk ambil scan terakhir.
     */
    public function lastScan()
    {
        $scan = RfidScan::latest('id')->first();

        if (! $scan) {
            return response()->json([
                'status' => 'empty',
            ]);
        }

        $uidHex = strtoupper($scan->uid_hex);
        $binding = RfidCard::with('mahasiswa')->where('uid_hex', $uidHex)->first();

        return response()->json([
            'status'      => 'ok',
            'uid_hex'     => $uidHex,
            'uid_pretty'  => implode(' ', str_split($uidHex, 2)),
            'registered'  => (bool) $binding,
            'mahasiswa'   => $binding && $binding->mahasiswa ? [
                'id'   => $binding->mahasiswa->id,
                'nim'  => $binding->mahasiswa->nim,
                'nama' => $binding->mahasiswa->nama,
            ] : null,
            'scanned_at'  => $scan->created_at,
        ]);
    }
}
