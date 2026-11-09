# Portal Akademik: Dashboard & RBAC

Tugas Mandiri PBKK Pertemuan 10. Panel admin AdminLTE dengan login Laravel Breeze dan otorisasi Spatie Laravel Permission.

![Beranda dashboard](doc/dashboard-admin.png)

## Instalasi

Butuh PHP 8.3+, Composer, dan Node.js.

```bash
composer run setup
php artisan serve
```

`composer run setup` memasang dependensi, membuat `.env` dan database SQLite, menjalankan migrasi beserta seeder, lalu mem-build aset. Buka `http://localhost:8000/login`.

Untuk mengulang data dari awal: `php artisan migrate:fresh --seed`.

## Akun Uji Coba (Auto-Login)

Di halaman login tersedia tombol **Login Admin** dan **Login Staf** (hanya tampil saat `APP_ENV=local`).

| Peran | Email | Password |
| --- | --- | --- |
| Admin | `admin@example.com` | `password` |
| Staf | `staf@example.com` | `password` |

## Hak Akses

Peran dan izin tersimpan di tabel Spatie (`roles`, `permissions`, `role_has_permissions`, dan lainnya).

| Izin | Admin | Staf |
| --- | --- | --- |
| `lihat-mahasiswa` | Ya | Ya |
| `tambah-mahasiswa` | Ya | Ya |
| `edit-mahasiswa` | Ya | Ya |
| `hapus-mahasiswa` | Ya | Tidak |

- Rute `/admin/*` dijaga middleware `auth`, `verified`, dan `role:Admin|Staf`.
- Tiap aksi di `MahasiswaController` dijaga middleware `permission:` (Laravel 13 memakai interface `HasMiddleware` sebagai pengganti `$this->middleware()` di constructor).
- Tombol Hapus hanya dirender lewat `@can('hapus-mahasiswa')`. Permintaan langsung dari Staf ke endpoint DELETE ditolak dengan 403.

## Fitur

- Tiga widget statistik dari `count()`: Mahasiswa, Dosen, dan Tugas Aktif.
- Tabel mahasiswa dengan pencarian nama/NRP, filter departemen dan status memakai `when()`, serta pagination `withQueryString()`.
- Seeder Faker: 60 mahasiswa, 10 dosen, 20 tugas.

## Pengujian

```bash
php artisan test --compact
```
