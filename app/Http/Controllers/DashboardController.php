<?php

namespace App\Http\Controllers;

use App\Models\Pharmacy;
use App\Models\Zone;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_pharmacies'   => Pharmacy::count(),
            'visited_pharmacies' => Pharmacy::whereNotIn('interest_status', ['non_visité'])->count(),
            'interested'         => Pharmacy::where('interest_status', 'intéressé')->count(),
            'new_clients'        => Pharmacy::where('interest_status', 'client')->count(),
            'conversion_rate'    => Pharmacy::count() > 0
                ? round((Pharmacy::where('interest_status', 'client')->count() / Pharmacy::count()) * 100, 1)
                : 0,
            'zones_covered'      => Zone::whereHas('pharmacies')->count(),
        ];

        $zones = Zone::withCount('pharmacies')->get();

        return view('dashboard', compact('user', 'stats', 'zones'));
    }
}
