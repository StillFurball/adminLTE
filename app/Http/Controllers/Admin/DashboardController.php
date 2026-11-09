<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Tugas;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the statistics widgets on the admin home page.
     */
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'totalMahasiswa' => Mahasiswa::count(),
            'totalDosen' => Dosen::count(),
            'totalTugasAktif' => Tugas::whereDate('tenggat', '>=', today())->count(),
        ]);
    }
}
