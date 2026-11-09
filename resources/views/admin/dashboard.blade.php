@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4">
            <x-adminlte-small-box :title="$totalMahasiswa" text="Total Mahasiswa" icon="bi bi-mortarboard" theme="primary"
                :url="route('admin.mahasiswa.index')" url-text="Kelola data" />
        </div>
        <div class="col-md-4">
            <x-adminlte-small-box :title="$totalDosen" text="Total Dosen" icon="bi bi-person-badge" theme="success" />
        </div>
        <div class="col-md-4">
            <x-adminlte-small-box :title="$totalTugasAktif" text="Total Tugas Aktif" icon="bi bi-journal-check" theme="warning" />
        </div>
    </div>

    @role('Admin')
        <div class="alert alert-info">Anda login sebagai <strong>Admin</strong> dengan akses penuh, termasuk menghapus data.</div>
    @else
        <div class="alert alert-secondary">Anda login sebagai <strong>Staf</strong> dengan akses terbatas: menghapus data tidak diizinkan.</div>
    @endrole
@endsection
