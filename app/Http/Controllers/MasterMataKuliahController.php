<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class MasterMataKuliahController extends Controller
{
    public function index()
    {
        $mataKuliah = MataKuliah::orderBy('nama_matkul')->paginate(10);
        return view('master.mata-kuliah.index', compact('mataKuliah'));
    }

    public function create()
    {
        return view('master.mata-kuliah.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_matkul' => 'required|string|max:50|unique:mata_kuliah,kode_matkul',
            'nama_matkul' => 'required|string|max:150',
        ]);

        MataKuliah::create($data);

        return redirect()
            ->route('master.matakuliah.index')
            ->with('success', 'Data mata kuliah berhasil ditambahkan.');
    }

    public function edit(MataKuliah $mata_kuliah)
    {
        // Parameter nama $mata_kuliah mengikuti binding dari Route::resource('mata-kuliah')
        return view('master.mata-kuliah.edit', ['mataKuliah' => $mata_kuliah]);
    }

    public function update(Request $request, MataKuliah $mata_kuliah)
    {
        $data = $request->validate([
            'kode_matkul' => 'required|string|max:50|unique:mata_kuliah,kode_matkul,' . $mata_kuliah->id,
            'nama_matkul' => 'required|string|max:150',
        ]);

        $mata_kuliah->update($data);

        return redirect()
            ->route('master.matakuliah.index')
            ->with('success', 'Data mata kuliah berhasil diperbarui.');
    }

    public function destroy(MataKuliah $mata_kuliah)
    {
        try {
            $mata_kuliah->delete();

            return redirect()
                ->route('master.matakuliah.index')
                ->with('success', 'Data mata kuliah berhasil dihapus.');
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                // Foreign key constraint (masih dipakai di tabel lain)
                return redirect()
                    ->route('master.matakuliah.index')
                    ->with('error', 'Mata kuliah tidak dapat dihapus karena masih digunakan pada data kelas.');
            }

            throw $e; // lempar error lain jika bukan karena constraint
        }
    }
}
