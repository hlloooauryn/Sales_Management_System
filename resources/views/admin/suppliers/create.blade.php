@extends('layouts.admin')

@section('title', 'Tambah Pemasok Baru')
@section('header_title', '🏢 Tambah Pemasok Bunga')

@section('content')
<div style="max-width: 680px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <h3>Formulir Data Pemasok</h3>
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline btn-sm">
                ← Kembali ke Daftar
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="name">Nama Perusahaan / Pemasok <span class="req">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: PT Lembang Fresh Flora" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="contact_person">Nama Kontak Person (PIC)</label>
                    <input type="text" name="contact_person" id="contact_person" class="form-control" value="{{ old('contact_person') }}" placeholder="Contoh: Bpk. Hendra Wijaya">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label" for="phone">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" placeholder="0812-3456-7890">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Alamat Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="sales@perusahaan.com">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="address">Alamat Perkebunan / Gudang</label>
                    <textarea name="address" id="address" class="form-control" rows="3" placeholder="Jl. Raya Maribaya No. 45, Lembang...">{{ old('address') }}</textarea>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 24px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        💾 Simpan Data Pemasok
                    </button>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
