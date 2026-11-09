@extends('adminlte::page')

@section('title', 'Data Mahasiswa')

@section('content_header')
    <h1>Data Mahasiswa</h1>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('admin.mahasiswa.index') }}" class="row g-2">
                <div class="col-md-4">
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Cari nama atau NRP" aria-label="Cari nama atau NRP">
                </div>
                <div class="col-md-3">
                    <select name="departemen" class="form-select" aria-label="Filter departemen">
                        <option value="">Semua departemen</option>
                        @foreach ($departemenList as $departemen)
                            <option value="{{ $departemen }}" @selected(request('departemen') === $departemen)>{{ $departemen }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select" aria-label="Filter status">
                        <option value="">Semua status</option>
                        <option value="1" @selected(request('status') === '1')>Aktif</option>
                        <option value="0" @selected(request('status') === '0')>Cuti</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @can('tambah-mahasiswa')
                        <a href="{{ route('admin.mahasiswa.create') }}" class="btn btn-success ms-auto">Tambah</a>
                    @endcan
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>NRP</th>
                        <th>Nama</th>
                        <th>Departemen</th>
                        <th>Dosen Wali</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mahasiswas as $mahasiswa)
                        <tr>
                            <td>{{ $mahasiswa->nrp }}</td>
                            <td>{{ $mahasiswa->nama }}</td>
                            <td>{{ $mahasiswa->departemen }}</td>
                            <td>{{ $mahasiswa->dosen->nama }}</td>
                            <td>
                                <span class="badge {{ $mahasiswa->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $mahasiswa->is_active ? 'Aktif' : 'Cuti' }}
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                                @can('edit-mahasiswa')
                                    <a href="{{ route('admin.mahasiswa.edit', $mahasiswa) }}" class="btn btn-sm btn-primary">Edit</a>
                                @endcan
                                @can('hapus-mahasiswa')
                                    <form method="POST" action="{{ route('admin.mahasiswa.destroy', $mahasiswa) }}" class="d-inline"
                                        onsubmit="return confirm('Hapus data mahasiswa ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Data tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $mahasiswas->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
