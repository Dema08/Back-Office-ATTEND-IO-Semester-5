<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use Illuminate\Http\Request;

class MasterDosenController extends Controller
{
    public function index()
    {
        $dosen = Dosen::orderBy('nama_dosen')->paginate(10);

        return view('master.dosen.index', compact('dosen'));
    }

    public function create()
    {
        return view('master.dosen.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nip'         => 'required|string|max:50|unique:dosen,nip',
            'nama_dosen'  => 'required|string|max:100',
            'email'       => 'nullable|email|max:100|unique:dosen,email',
        ]);

        Dosen::create($data);

        return redirect()
            ->route('master.dosen.index')
            ->with('success', 'Data dosen berhasil ditambahkan.');
    }

    public function edit(Dosen $dosen)
    {
        return view('master.dosen.edit', compact('dosen'));
    }

    public function update(Request $request, Dosen $dosen)
    {
        $data = $request->validate([
            'nip'         => 'required|string|max:50|unique:dosen,nip,' . $dosen->id,
            'nama_dosen'  => 'required|string|max:100',
            'email'       => 'nullable|email|max:100',
        ]);

        $dosen->update($data);

        return redirect()
            ->route('master.dosen.index')
            ->with('success', 'Data dosen berhasil diperbarui.');
    }

    public function destroy(Dosen $dosen)
    {
        $dosen->delete();

        return redirect()
            ->route('master.dosen.index')
            ->with('success', 'Data dosen berhasil dihapus.');
    }
}
