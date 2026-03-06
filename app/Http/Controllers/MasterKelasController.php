<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class MasterKelasController extends Controller
{
    public function index()
    {
        // ambil relasi dosen & mataKuliah
        $kelas = Kelas::with(['dosen', 'mataKuliah'])
            ->orderBy('golongan')
            ->get()//->paginate(10); - pagination disabled
            ->groupBy('golongan');

        return view('master.kelas.index', compact('kelas'));
    }

    public function create()
    {
        $mataKuliah = MataKuliah::orderBy('nama_matkul')->get();
        $dosen      = Dosen::orderBy('nama_dosen')->get();

        return view('master.kelas.create', compact('mataKuliah', 'dosen'));
    }

    public function store(Request $request)
    {
        // ⬇️ TIDAK ADA unique di golongan
        $data = $request->validate([
            'golongan'       => [
                'required',
                'string',
                'max:50',
                Rule::unique('kelas')->where(function ($q) use ($request) {
                    return $q->where('mata_kuliah_id', $request->mata_kuliah_id)
                             ->where('dosen_id', $request->dosen_id);
                }),
                ],
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'dosen_id'       => 'required|exists:dosen,id',
        ], [
            'golongan.unique' => 'Kelas dengan golongan, mata kuliah, dan dosen tersebut sudah ada.',
        ]);


        Kelas::create($data);

        return redirect()
            ->route('master.kelas.index')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kelas)
    {
        $mataKuliah = MataKuliah::orderBy('nama_matkul')->get();
        $dosen      = Dosen::orderBy('nama_dosen')->get();

        return view('master.kelas.edit', compact('kelas', 'mataKuliah', 'dosen'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        // ⬇️ TIDAK ADA unique di golongan
        $data = $request->validate([
            'golongan'       => [
                'required',
                'string',
                'max:50',
                Rule::unique('kelas')->ignore($kelas->id)->where(function ($q) use ($request) {
                    return $q->where('mata_kuliah_id', $request->mata_kuliah_id)
                             ->where('dosen_id', $request->dosen_id);
                }),
                  ],
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'dosen_id'       => 'required|exists:dosen,id',
        ], [
            'golongan.unique' => 'Kelas dengan golongan, mata kuliah, dan dosen tersebut sudah ada.',
        ]);

        $kelas->update($data);

        return redirect()
            ->route('master.kelas.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();

        return redirect()
            ->route('master.kelas.index')
            ->with('success', 'Data kelas berhasil dihapus.');
    }
}
