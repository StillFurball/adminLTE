<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MahasiswaRequest;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MahasiswaController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, Middleware>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:lihat-mahasiswa', only: ['index']),
            new Middleware('permission:tambah-mahasiswa', only: ['create', 'store']),
            new Middleware('permission:edit-mahasiswa', only: ['edit', 'update']),
            new Middleware('permission:hapus-mahasiswa', only: ['destroy']),
        ];
    }

    /**
     * Display the filtered, paginated list of mahasiswa.
     */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'departemen' => ['nullable', Rule::in(Mahasiswa::DEPARTEMEN)],
            'status' => ['nullable', 'boolean'],
        ]);

        $mahasiswas = Mahasiswa::query()
            ->with('dosen')
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->where(
                fn (Builder $query) => $query->where('nama', 'like', "%{$search}%")->orWhere('nrp', 'like', "%{$search}%")
            ))
            ->when($filters['departemen'] ?? null, fn (Builder $query, string $departemen) => $query->where('departemen', $departemen))
            ->when(isset($filters['status']), fn (Builder $query) => $query->where('is_active', $filters['status']))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.mahasiswa.index', [
            'mahasiswas' => $mahasiswas,
            'departemenList' => Mahasiswa::DEPARTEMEN,
        ]);
    }

    /**
     * Show the form for creating a mahasiswa.
     */
    public function create(): View
    {
        return $this->form(new Mahasiswa);
    }

    /**
     * Store a new mahasiswa.
     */
    public function store(MahasiswaRequest $request): RedirectResponse
    {
        Mahasiswa::create($request->validated());

        return to_route('admin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }

    /**
     * Show the form for editing a mahasiswa.
     */
    public function edit(Mahasiswa $mahasiswa): View
    {
        return $this->form($mahasiswa);
    }

    /**
     * Update the given mahasiswa.
     */
    public function update(MahasiswaRequest $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $mahasiswa->update($request->validated());

        return to_route('admin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    /**
     * Delete the given mahasiswa.
     */
    public function destroy(Mahasiswa $mahasiswa): RedirectResponse
    {
        $mahasiswa->delete();

        return back()->with('success', 'Data mahasiswa berhasil dihapus.');
    }

    /**
     * Build the shared create/edit form view.
     */
    private function form(Mahasiswa $mahasiswa): View
    {
        return view('admin.mahasiswa.form', [
            'mahasiswa' => $mahasiswa,
            'departemenList' => Mahasiswa::DEPARTEMEN,
            'dosens' => Dosen::orderBy('nama')->get(),
        ]);
    }
}
