<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard dengan ringkasan statistik.
     */
    public function index(): Response
    {
        return Inertia::render('Dashboard', [
            'totals' => [
                'totalKelompok' => \App\Models\Kelompok::count(),
                'totalUsers' => \App\Models\User::count(),
                'totalKantor' => \App\Models\Kantor::count(),
                'totalAnggota' => \App\Models\Anggota::count(),
                'totalAccgroup' => \App\Models\AccGroup::count(),
                'totalAccheader' => \App\Models\AccHeader::count(),
                'totalAccount' => \App\Models\Account::count(),
            ],
        ]);
    }
}
