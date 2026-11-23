@extends('layouts.admin')

@section('title', 'Pengguna & peran — Empirise Jalan-Jalan')
@section('content')
    <header class="mb-8">
        <p class="text-xs font-bold tracking-[0.18em] text-brand">AKSES</p>
        <h1 class="mt-2 text-3xl font-bold sm:text-4xl">Pengguna &amp; peran</h1>
        <p class="mt-2 text-copy">Perubahan akses tercatat di audit log. Peran akun sendiri tidak dapat diubah.</p>
    </header>
    <details class="mb-6 rounded-3xl border border-line p-5">
        <summary class="cursor-pointer font-bold">Tambah akun admin</summary>
        <form class="mt-4 grid gap-3 sm:grid-cols-2" action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <label class="text-sm font-semibold">Nama<input class="mt-1 w-full rounded-xl border-line" name="name" required></label>
            <label class="text-sm font-semibold">Email<input class="mt-1 w-full rounded-xl border-line" type="email" name="email" required></label>
            <label class="text-sm font-semibold">Kata sandi (min. 12 karakter)<input class="mt-1 w-full rounded-xl border-line" type="password" name="password" required></label>
            <label class="text-sm font-semibold">Konfirmasi kata sandi<input class="mt-1 w-full rounded-xl border-line" type="password" name="password_confirmation" required></label>
            <div class="sm:col-span-2"><button class="rounded-full bg-brand px-5 py-2.5 text-sm font-bold text-white" type="submit">Buat akun admin</button></div>
        </form>
    </details>
    <form class="mb-5 flex gap-2" action="{{ route('admin.users.index') }}" method="GET">
        <label class="sr-only" for="user-search">Cari nama atau email</label>
        <input class="min-w-0 flex-1 rounded-full border-line" id="user-search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama atau email">
        <button class="rounded-full bg-ink px-5 py-2 text-sm font-semibold text-white" type="submit">Cari</button>
    </form>
    <div class="overflow-x-auto rounded-3xl border border-line">
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase tracking-wide text-copy"><tr><th class="px-5 py-4">Nama</th><th class="px-5 py-4">Email</th><th class="px-5 py-4">Peran</th><th class="px-5 py-4">Akun</th></tr></thead>
            <tbody class="divide-y divide-line">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-5 py-4 font-semibold">{{ $user->name }}</td><td class="px-5 py-4">{{ $user->email }}</td>
                        <td class="px-5 py-4">
                            @if (auth()->id() === $user->id)
                                <span class="rounded-full bg-gray-100 px-3 py-2">{{ $user->role->value === 'admin' ? 'Admin' : 'Klien' }} · Anda</span>
                            @else
                                <form action="{{ route('admin.users.role', $user) }}" method="POST" onsubmit="return confirm('Ubah peran pengguna ini?')">
                                    @csrf @method('PATCH')
                                    <label class="sr-only" for="role-{{ $user->id }}">Peran {{ $user->name }}</label>
                                    <select class="rounded-full border-line" id="role-{{ $user->id }}" name="role" onchange="this.form.submit()">
                                        <option value="admin" @selected($user->role->value === 'admin')>Admin</option>
                                        <option value="klien" @selected($user->role->value === 'klien')>Klien</option>
                                    </select>
                                </form>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if (auth()->id() === $user->id)
                                <span class="text-copy">Aktif</span>
                            @else
                                <form action="{{ route('admin.users.active', $user) }}" method="POST" onsubmit="return confirm('{{ $user->aktif ? 'Nonaktifkan' : 'Aktifkan' }} akun ini?')">
                                    @csrf @method('PATCH')
                                    <button class="rounded-full px-3 py-2 text-xs font-semibold {{ $user->aktif ? 'bg-green-50 text-green-800' : 'bg-gray-100 text-copy' }}" type="submit">{{ $user->aktif ? 'Aktif' : 'Nonaktif' }} · Ubah</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $users->links() }}</div>
@endsection
