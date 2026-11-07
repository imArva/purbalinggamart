@extends('layouts.app')
@section('title', 'Kelola Akun')
@section('eyebrow', 'MANAJEMEN PENGGUNA')
@section('page-title', 'Kelola Akun')
@section('content')
<div class="section-heading">
    <div><h2>Akun Manajer dan Kasir</h2><p>Buat, perbarui, atau nonaktifkan akses pengguna toko.</p></div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAccountModal"><i class="bi bi-person-plus me-2"></i>Tambah akun</button>
</div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">Periksa kembali data akun yang diisi.</div>@endif
<form class="toolbar mb-3" method="GET" action="{{ route('accounts.index') }}">
    <div class="search-box"><i class="bi bi-search"></i><input name="q" class="form-control" placeholder="Cari nama atau email..." value="{{ request('q') }}"></div>
    <button class="btn btn-primary">Cari</button>
</form>
<section class="card-panel"><div class="table-responsive"><table class="table">
    <thead><tr><th>Pengguna</th><th>Peran</th><th>Status</th><th>Dibuat</th><th class="text-end">Aksi</th></tr></thead>
    <tbody>
    @forelse($users as $user)
        <tr>
            <td><div class="table-title">{{ $user->name }}</div><div class="table-sub">{{ $user->email }}</div></td>
            <td><span class="badge-soft primary">{{ ucfirst($user->role) }}</span></td>
            <td><span class="badge-soft {{ $user->is_active ? 'success' : 'neutral' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
            <td>{{ optional($user->created_at)->format('d M Y') }}</td>
            <td class="text-end">
                <button class="btn btn-light btn-icon" data-bs-toggle="modal" data-bs-target="#editAccount{{ $user->id }}" aria-label="Edit akun"><i class="bi bi-pencil"></i></button>
                <form class="d-inline" method="POST" action="{{ route('accounts.destroy', $user) }}" onsubmit="return confirm('Hapus akun ini?')">@csrf @method('DELETE')<button class="btn btn-light btn-icon text-danger" aria-label="Hapus akun"><i class="bi bi-trash3"></i></button></form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5"><div class="empty-state"><i class="bi bi-people"></i><strong>Belum ada akun</strong><span>Tambahkan akun manajer atau kasir.</span></div></td></tr>
    @endforelse
    </tbody>
</table></div></section>

<div class="modal fade" id="createAccountModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <form method="POST" action="{{ route('accounts.store') }}">@csrf
        <div class="modal-header"><h5 class="modal-title">Tambah akun</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Nama lengkap</label><input name="name" class="form-control" value="{{ old('name') }}" required></div>
            <div class="mb-3"><label class="form-label">Email</label><input name="email" type="email" class="form-control" value="{{ old('email') }}" required></div>
            <div class="row g-3"><div class="col-md-6"><label class="form-label">Peran</label><select name="role" class="form-select" required><option value="kasir">Kasir</option><option value="manajer">Manajer</option></select></div><div class="col-md-6"><label class="form-label">Status</label><select name="is_active" class="form-select"><option value="1">Aktif</option><option value="0">Nonaktif</option></select></div></div>
            <div class="row g-3 mt-1"><div class="col-md-6"><label class="form-label">Password</label><input name="password" type="password" class="form-control" required></div><div class="col-md-6"><label class="form-label">Konfirmasi password</label><input name="password_confirmation" type="password" class="form-control" required></div></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan akun</button></div>
    </form>
</div></div></div>

@foreach($users as $user)
<div class="modal fade" id="editAccount{{ $user->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <form method="POST" action="{{ route('accounts.update', $user) }}">@csrf @method('PUT')
        <div class="modal-header"><h5 class="modal-title">Edit akun</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Nama lengkap</label><input name="name" class="form-control" value="{{ $user->name }}" required></div>
            <div class="mb-3"><label class="form-label">Email</label><input name="email" type="email" class="form-control" value="{{ $user->email }}" required></div>
            <div class="row g-3"><div class="col-md-6"><label class="form-label">Peran</label><select name="role" class="form-select" required><option value="kasir" @selected($user->role === 'kasir')>Kasir</option><option value="manajer" @selected($user->role === 'manajer')>Manajer</option></select></div><div class="col-md-6"><label class="form-label">Status</label><select name="is_active" class="form-select"><option value="1" @selected($user->is_active)>Aktif</option><option value="0" @selected(!$user->is_active)>Nonaktif</option></select></div></div>
            <div class="mt-3"><label class="form-label">Password baru <span class="text-muted">(opsional)</span></label><input name="password" type="password" class="form-control"></div>
            <div class="mt-3"><label class="form-label">Konfirmasi password</label><input name="password_confirmation" type="password" class="form-control"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan perubahan</button></div>
    </form>
</div></div></div>
@endforeach
@endsection
