<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ZoneController extends Controller
{
    public function index()
    {
        $zones = Zone::withCount('pharmacies')->with('agent')->orderBy('name')->get();
        return view('zones.index', compact('zones'));
    }

    public function create()
    {
        $agents = User::role('Agent terrain')->get();
        return view('zones.create', compact('agents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:100|unique:zones,name',
            'status'             => 'required|in:active,inactive',
            'agent_id'           => 'nullable|exists:users,id',
            'target_pharmacies'  => 'nullable|integer|min:0',
            'description'        => 'nullable|string|max:500',
        ]);

        Zone::create($validated);

        return redirect()->route('zones.index')
            ->with('success', 'Zone "' . $validated['name'] . '" créée avec succès.');
    }

    public function show(Zone $zone)
    {
        $zone->load(['agent', 'pharmacies.creator']);
        return view('zones.show', compact('zone'));
    }

    public function edit(Zone $zone)
    {
        $agents = User::role('Agent terrain')->get();
        return view('zones.edit', compact('zone', 'agents'));
    }

    public function update(Request $request, Zone $zone)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:100|unique:zones,name,' . $zone->id,
            'status'             => 'required|in:active,inactive',
            'agent_id'           => 'nullable|exists:users,id',
            'target_pharmacies'  => 'nullable|integer|min:0',
            'description'        => 'nullable|string|max:500',
        ]);

        $zone->update($validated);

        return redirect()->route('zones.index')
            ->with('success', 'Zone mise à jour avec succès.');
    }

    public function destroy(Zone $zone)
    {
        if ($zone->pharmacies()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer une zone contenant des pharmacies.');
        }

        $zone->delete();
        return redirect()->route('zones.index')
            ->with('success', 'Zone supprimée avec succès.');
    }
}
