<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\RfidTag;
use App\Models\RfidScan;
use Illuminate\Http\Request;

class RfidController extends Controller
{
    public function index()
    {
        // pakai nama variabel $rfids supaya sama dengan di blade
        $rfids = RfidTag::with('mahasiswa')
            ->orderBy('uid_hex')
            ->get();

        return view('rfid.index', [
            'rfids' => $rfids,
        ]);
    }

 public function create(Request $request)
{
    $selectedMahasiswaId = $request->query('mahasiswa_id');

    // default: HANYA yang belum punya RFID
    $mahasiswa = Mahasiswa::doesntHave('rfidTag')
        ->orderBy('nim')
        ->get();

    // Kalau datang dari tombol "Daftarkan RFID" dengan ?mahasiswa_id=...
    if ($selectedMahasiswaId) {
        $m = Mahasiswa::with('rfidTag')->findOrFail($selectedMahasiswaId);

        // Kalau mahasiswa ini SUDAH punya RFID → balikin ke index dengan pesan error
        if ($m->rfidTag) {
            return redirect()
                ->route('rfid.index')
                ->with('error', 'Mahasiswa ini sudah memiliki kartu RFID.');
        }

        // Kalau belum punya dan belum ada di koleksi (misal karena filter lain)
        if (! $mahasiswa->contains('id', $m->id)) {
            $mahasiswa->prepend($m);   // taruh di paling atas & terpilih
        }
    }

    return view('rfid.create', compact('mahasiswa', 'selectedMahasiswaId'));
}


public function store(Request $request)
{
    $data = $request->validate([
        'mahasiswa_id' => 'required|exists:mahasiswa,id',
        'uid_hex'      => 'required|string',
    ]);

    // normalisasi UID seperti sebelumnya...
    $uid = strtoupper(preg_replace('/[^0-9A-Fa-f]/', '', $data['uid_hex']));

    // ⬇️ CEK: mahasiswa sudah punya RFID atau belum
    $alreadyForMahasiswa = RfidTag::where('mahasiswa_id', $data['mahasiswa_id'])->first();
    if ($alreadyForMahasiswa) {
        return back()
            ->withErrors([
                'mahasiswa_id' => 'Mahasiswa ini sudah memiliki kartu RFID dengan UID '
                    . $alreadyForMahasiswa->uid_hex,
            ])
            ->withInput();
    }

    // ⬇️ CEK: UID sudah dipakai orang lain atau belum (kode kamu yang lama)
    $existing = RfidTag::with('mahasiswa')
        ->where('uid_hex', $uid)
        ->first();

    if ($existing) {
        return back()
            ->withErrors([
                'uid_hex' => 'UID sudah terdaftar untuk '
                    . ($existing->mahasiswa->nim ?? '-') . ' - ' . ($existing->mahasiswa->nama ?? '-'),
            ])
            ->withInput();
    }

    // baru buat
    RfidTag::create([
        'mahasiswa_id' => $data['mahasiswa_id'],
        'uid_hex'      => $uid,
    ]);

    return redirect()
        ->route('rfid.index')
        ->with('success', 'Kartu RFID berhasil didaftarkan.');
}


    public function destroy(RfidTag $rfid)
    {
        $rfid->delete();

        return redirect()
            ->route('rfid.index')
            ->with('success', 'Kartu RFID dihapus.');
    }
}
