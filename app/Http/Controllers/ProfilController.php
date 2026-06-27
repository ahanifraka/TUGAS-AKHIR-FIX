<?php

namespace App\Http\Controllers;

use App\Models\Pejabat;
use Inertia\Inertia;

class ProfilController extends Controller
{
    public function tentangkami()
    {
        return Inertia::render('Profil/TentangKami');
    }

    public function visimisi()
    {
        return Inertia::render('Profil/VisiMisi');
    }
    public function tupoksi()
    {
        return Inertia::render('Profil/TugasPokokDanFungsi');
    }
    public function strukturorganisasi()
    {
        return Inertia::render('Profil/StrukturOrganisasi');
    }
    public function pejabat()
    {
        $pejabats = Pejabat::where('published', true)
            ->orderBy('order', 'asc')
            ->get();

        return Inertia::render('Profil/Pejabat', [
            'pejabats' => $pejabats,
        ]);
    }
    public function rencanastrategis()
    {
        return Inertia::render('Profil/RencanaStrategis');
    }
}
