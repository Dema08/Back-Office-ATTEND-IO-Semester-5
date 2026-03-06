<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use App\Models\RfidTag;


class MasterMahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->query('search');
        $golongan = $request->query('golongan');

        $mahasiswaTable = (new Mahasiswa)->getTable(); // biar aman nama tabelnya
        $rfidTable      = 'rfid_tags';                 // sesuaikan kalau beda

    $query = Mahasiswa::query()
        ->with('rfidTag')                         // relasi hasOne rfidTag
        ->leftJoin($rfidTable, $rfidTable.'.mahasiswa_id', '=', $mahasiswaTable.'.id')
        ->select($mahasiswaTable.'.*');

    // === SEARCH nama / NIM ===
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('nama', 'like', '%'.$search.'%')
              ->orWhere('nim', 'like', '%'.$search.'%');
        });
    }

    // === FILTER GOLONGAN ===
    if ($golongan) {
        $query->where($mahasiswaTable.'.golongan', $golongan);
    }

    // === URUTKAN: yang punya RFID dulu, baru yang belum ===
    $query->orderByRaw("CASE WHEN {$rfidTable}.id IS NULL THEN 1 ELSE 0 END")
          ->orderBy($mahasiswaTable.'.nama'); // urut nama di dalam tiap grup

    $mahasiswa = $query->paginate(10)->withQueryString();

    // Untuk isi dropdown golongan
    $golonganList = Mahasiswa::select('golongan')
        ->whereNotNull('golongan')
        ->distinct()
        ->orderBy('golongan')
        ->pluck('golongan');

        return view('master.mahasiswa.index', compact('mahasiswa', 'golonganList'));
    }

    public function create()
    {
        // ambil semua kelas untuk dropdown
        $kelas = Kelas::with('mataKuliah')
            ->orderBy('golongan')
            ->get();

        return view('master.mahasiswa.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nim'      => 'required|string|max:20|unique:mahasiswa,nim',
            'nama'     => 'required|string|max:100',
            'email'    => 'nullable|email|max:100|unique:mahasiswa,email',
            'golongan' => 'nullable|string|max:20', // atau 'required' kalau mau wajib pilih
            // 'jenis_kelamin' => 'required|in:L,P', // kalau nanti dipakai
        ]);

        Mahasiswa::create($data);

        return redirect()
            ->route('master.mahasiswa.index')
            ->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        $kelas = Kelas::with('mataKuliah')
            ->orderBy('golongan')
            ->get();

        return view('master.mahasiswa.edit', compact('mahasiswa', 'kelas'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $data = $request->validate([
            'nim'      => 'required|string|max:20|unique:mahasiswa,nim,' . $mahasiswa->id,
            'nama'     => 'required|string|max:100',
            'email'    => 'nullable|email|max:100|unique:mahasiswa,email,' . $mahasiswa->id,
            'golongan' => 'nullable|string|max:20',
            'uid_hex'  => 'nullable|string',
        ]);

         // 2) Pisahkan data mahasiswa & UID RFID
    $uidInput = $data['uid_hex'] ?? null;
    unset($data['uid_hex']); // biar nggak dikirim ke tabel mahasiswa

    // 3) Update data mahasiswa dulu
    $mahasiswa->update($data);

    // 4) Logika update RFID
    //    - kalau kosong → hapus relasi RFID (kalau ada)
    //    - kalau diisi → normalisasi & simpan/update di RfidTag
    if ($uidInput === null || trim($uidInput) === '') {
        // kosong → hapus RFID kalau ada
        if ($mahasiswa->rfidTag) {
            $mahasiswa->rfidTag->delete();
        }
    } else {
        // normalisasi UID: buang spasi & non-hex, jadikan uppercase
        $uid = strtoupper(preg_replace('/[^0-9A-Fa-f]/', '', $uidInput));

        if (strlen($uid) < 8) {
            return back()
                ->withErrors(['uid_hex' => 'Format UID tidak valid. Minimal 4 byte (8 karakter hex).'])
                ->withInput();
        }

        // cek apakah UID sudah dipakai mahasiswa lain
        $exists = RfidTag::where('uid_hex', $uid)
            ->where('mahasiswa_id', '!=', $mahasiswa->id)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['uid_hex' => 'UID sudah digunakan oleh mahasiswa lain.'])
                ->withInput();
        }

        // kalau mahasiswa sudah punya RFID → update
        if ($mahasiswa->rfidTag) {
            $mahasiswa->rfidTag->update([
                'uid_hex' => $uid,
            ]);
        } else {
            // belum punya → buat baru
            RfidTag::create([
                'mahasiswa_id' => $mahasiswa->id,
                'uid_hex'      => $uid,
            ]);
        }
    }

    return redirect()
        ->route('master.mahasiswa.index')
        ->with('success', 'Data mahasiswa berhasil diperbarui.');
}

    public function destroy(Mahasiswa $mahasiswa)
    {
        $mahasiswa->delete();

        return redirect()
            ->route('master.mahasiswa.index')
            ->with('success', 'Data mahasiswa berhasil dihapus.');
    }
}
