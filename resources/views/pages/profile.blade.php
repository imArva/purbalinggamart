@extends('layouts.app')
@section('title', 'Profil')
@section('eyebrow', 'AKUN PENGGUNA')
@section('page-title', 'Profil')
@section('content')
<div class="section-heading">
    <div>
        <h2>Profil saya</h2>
        <p>Kelola informasi akun dan keamanan password Anda.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="profile-layout">
    <section class="card-panel profile-summary">
        <div class="profile-large-avatar">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
        <h2>{{ $user->name }}</h2>
        <p>{{ $user->email }}</p>
        <span class="badge-soft primary">{{ ucfirst($user->role) }}</span>
        <div class="profile-meta">
            <div><i class="bi bi-calendar3"></i><span>Bergabung {{ optional($user->created_at)->format('d M Y') }}</span></div>
            <div><i class="bi bi-shield-check"></i><span>Akun {{ $user->is_active ? 'aktif' : 'nonaktif' }}</span></div>
        </div>
    </section>

    <section class="card-panel">
        <div class="card-panel-header">
            <div>
                <h3>Informasi akun</h3>
                <p>Perubahan email dan nama akan langsung tersimpan.</p>
            </div>
        </div>
        <form method="POST" action="{{ route('profile.update') }}" class="profile-form">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="name">Nama lengkap</label>
                    <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="email">Email</label>
                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <hr>
            <h4>Ubah password</h4>
            <p class="form-help">Kosongkan bagian ini jika tidak ingin mengubah password.</p>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="current_password">Password saat ini</label>
                    <input id="current_password" name="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror">
                    @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="password">Password baru</label>
                    <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="password_confirmation">Konfirmasi password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control">
                </div>
            </div>
            <div class="text-end mt-4">
                <button class="btn btn-primary" type="submit"><i class="bi bi-check2 me-1"></i>Simpan perubahan</button>
            </div>
        </form>
    </section>
</div>
@endsection
