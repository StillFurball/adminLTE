@extends('adminlte::page')

@section('title', $mahasiswa->exists ? 'Edit Mahasiswa' : 'Tambah Mahasiswa')

@section('content_header')
    <h1>{{ $mahasiswa->exists ? 'Edit Mahasiswa' : 'Tambah Mahasiswa' }}</h1>
@endsection

@section('content')
    <div class="card">
        <form method="POST" action="{{ $mahasiswa->exists ? route('admin.mahasiswa.update', $mahasiswa) : route('admin.mahasiswa.store') }}">
            @csrf
            @if ($mahasiswa->exists)
                @method('PUT')
            @endif

            <div class="card-body">
                <div class="mb-3">
                    <label for="nrp" class="form-label">NRP</label>
                    <input id="nrp" name="nrp" value="{{ old('nrp', $mahasiswa->nrp) }}" required
                        class="form-control @error('nrp') is-invalid @enderror">
                    @error('nrp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input id="nama" name="nama" value="{{ old('nama', $mahasiswa->nama) }}" required
                        class="form-control @error('nama') is-invalid @enderror">
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="departemen" class="form-label">Departemen</label>
                    <select id="departemen" name="departemen" required class="form-select @error('departemen') is-invalid @enderror">
                        @foreach ($departemenList as $departemen)
                            <option value="{{ $departemen }}" @selected(old('departemen', $mahasiswa->departemen) === $departemen)>{{ $departemen }}</option>
                        @endforeach
                    </select>
                    @error('departemen')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="dosen_id" class="form-label">Dosen Wali</label>
                    <select id="dosen_id" name="dosen_id" required class="form-select @error('dosen_id') is-invalid @enderror">
                        @foreach ($dosens as $dosen)
                            <option value="{{ $dosen->id }}" @selected((int) old('dosen_id', $mahasiswa->dosen_id) === $dosen->id)>{{ $dosen->nama }}</option>
                        @endforeach
                    </select>
                    @error('dosen_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="is_active" class="form-label">Status</label>
                    <select id="is_active" name="is_active" required class="form-select @error('is_active') is-invalid @enderror">
                        <option value="1" @selected((bool) old('is_active', $mahasiswa->is_active ?? true))>Aktif</option>
                        <option value="0" @selected(! old('is_active', $mahasiswa->is_active ?? true))>Cuti</option>
                    </select>
                    @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
