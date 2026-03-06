@extends('layouts.main')

@section('title', 'Master User')
@section('page_title', 'Master User')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold">Daftar User</h2>
            <p class="text-xs text-slate-400">Kelola akun pengguna (admin / user sistem).</p>
        </div>
        <a href="{{ route('master.user.create') }}"
           class="inline-flex items-center rounded-xl bg-indigo-500 px-4 py-2 text-xs font-semibold text-white shadow-lg shadow-indigo-500/40">
            + Tambah User
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-2 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-xl border border-rose-500/40 bg-rose-500/10 px-4 py-2 text-sm text-rose-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-2xl bg-slate-900/70 border border-slate-800/80">
        <table class="min-w-full text-xs">
            <thead class="text-slate-400 border-b border-slate-800/80">
                <tr>
                    <th class="py-2 px-4 text-left">No</th>
                    <th class="py-2 px-4 text-left">Nama</th>
                    <th class="py-2 px-4 text-left">Email</th>
                    <th class="py-2 px-4 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80 text-slate-200">
                @forelse ($users as $i => $user)
                    <tr>
                        <td class="py-2 px-4">{{ $users->firstItem() + $i }}</td>
                        <td class="py-2 px-4">{{ $user->name }}</td>
                        <td class="py-2 px-4">{{ $user->email }}</td>
                        <td class="py-2 px-4 space-x-2">
                            <a href="{{ route('master.user.edit', $user) }}"
                               class="inline-flex items-center text-xs text-indigo-400 hover:text-indigo-300">
                                ✏️ Edit
                            </a>

                            @if(auth()->id() !== $user->id)
                                <form action="{{ route('master.user.destroy', $user) }}" method="POST" class="inline-block"
                                      onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs text-rose-400 hover:text-rose-300">
                                        🗑 Hapus
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-4 px-4 text-center text-slate-500">
                            Belum ada user.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3 border-t border-slate-800/80">
            {{ $users->links() }}
        </div>
    </div>
@endsection
