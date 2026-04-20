<?php

namespace App\Http\Controllers;

use App\Models\Pharmacy;
use App\Models\Zone;
use App\Services\PharmacyXlsxImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class PharmacyController extends Controller
{
    public function index(Request $request)
    {
        $query = Pharmacy::with(['zone', 'creator'])->latest();

        // Recherche
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('owner_name', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%");
            });
        }

        // Filtre par zone
        if ($zoneId = $request->get('zone_id')) {
            $query->where('zone_id', $zoneId);
        }

        // Filtre par intérêt
        if ($interest = $request->get('interest_status')) {
            $query->where('interest_status', $interest);
        }

        $pharmacies = $query->paginate(15)->withQueryString();
        $zones = Zone::where('status', 'active')->orderBy('name')->get();

        return view('pharmacies.index', compact('pharmacies', 'zones'));
    }

    public function create()
    {
        $zones = Zone::where('status', 'active')->orderBy('name')->get();

        return view('pharmacies.create', compact('zones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'zone_id' => 'required|exists:zones,id',
            'name' => 'required|string|max:150',
            'owner_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'type' => 'required|in:publique,privée,clinique',
            'best_selling_products' => 'nullable|string',
            'stock_problem' => 'boolean',
            'delivery_problem' => 'boolean',
            'training_need' => 'boolean',
            'distribution_need' => 'boolean',
            'interest_status' => 'required|in:non_visité,visité,intéressé,non_intéressé,client',
            'partnership_type' => 'required|in:aucun,distributeur,partenaire,client_direct',
            'notes' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['stock_problem'] = $request->boolean('stock_problem');
        $validated['delivery_problem'] = $request->boolean('delivery_problem');
        $validated['training_need'] = $request->boolean('training_need');
        $validated['distribution_need'] = $request->boolean('distribution_need');

        Pharmacy::create($validated);

        return redirect()->route('pharmacies.index')
            ->with('success', 'Pharmacie "'.$validated['name'].'" ajoutée avec succès.');
    }

    public function show(Pharmacy $pharmacy)
    {
        $pharmacy->load(['zone', 'creator', 'visits.agent', 'client.commercial']);

        return view('pharmacies.show', compact('pharmacy'));
    }

    public function edit(Pharmacy $pharmacy)
    {
        $zones = Zone::where('status', 'active')->orderBy('name')->get();

        return view('pharmacies.edit', compact('pharmacy', 'zones'));
    }

    public function update(Request $request, Pharmacy $pharmacy)
    {
        $validated = $request->validate([
            'zone_id' => 'required|exists:zones,id',
            'name' => 'required|string|max:150',
            'owner_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'type' => 'required|in:publique,privée,clinique',
            'best_selling_products' => 'nullable|string',
            'stock_problem' => 'boolean',
            'delivery_problem' => 'boolean',
            'training_need' => 'boolean',
            'distribution_need' => 'boolean',
            'interest_status' => 'required|in:non_visité,visité,intéressé,non_intéressé,client',
            'partnership_type' => 'required|in:aucun,distributeur,partenaire,client_direct',
            'notes' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $validated['stock_problem'] = $request->boolean('stock_problem');
        $validated['delivery_problem'] = $request->boolean('delivery_problem');
        $validated['training_need'] = $request->boolean('training_need');
        $validated['distribution_need'] = $request->boolean('distribution_need');

        $pharmacy->update($validated);

        return redirect()->route('pharmacies.index')
            ->with('success', 'Pharmacie mise à jour avec succès.');
    }

    public function destroy(Pharmacy $pharmacy)
    {
        $pharmacy->delete();

        return redirect()->route('pharmacies.index')
            ->with('success', 'Pharmacie supprimée.');
    }

    public function destroyAll()
    {
        $deleted = Pharmacy::query()->count();
        Pharmacy::query()->delete();

        return redirect()->route('pharmacies.index')
            ->with('success', "{$deleted} pharmacie(s) supprimée(s).");
    }

    public function destroySelected(Request $request)
    {
        $validated = $request->validate([
            'pharmacy_ids' => 'required|array|min:1',
            'pharmacy_ids.*' => 'integer|exists:pharmacies,id',
        ], [
            'pharmacy_ids.required' => 'Veuillez sélectionner au moins une pharmacie.',
            'pharmacy_ids.min' => 'Veuillez sélectionner au moins une pharmacie.',
        ]);

        $deleted = Pharmacy::query()
            ->whereIn('id', $validated['pharmacy_ids'])
            ->delete();

        return redirect()->route('pharmacies.index')
            ->with('success', "{$deleted} pharmacie(s) sélectionnée(s) supprimée(s).");
    }

    public function import(Request $request, PharmacyXlsxImportService $importService)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:xlsx|max:5120',
        ], [
            'file.required' => 'Veuillez sélectionner un fichier Excel (.xlsx).',
            'file.mimes' => 'Le fichier doit être au format .xlsx.',
            'file.max' => 'Le fichier ne doit pas dépasser 5 Mo.',
        ]);

        try {
            $result = $importService->import($validated['file']);
        } catch (RuntimeException $exception) {
            return redirect()->route('pharmacies.index')
                ->with('error', $exception->getMessage());
        }

        $message = "Import terminé: {$result['created']} créée(s), {$result['updated']} mise(s) à jour, {$result['skipped']} ignorée(s).";
        if (($result['zones_created'] ?? 0) > 0) {
            $message .= " {$result['zones_created']} zone(s) créée(s) automatiquement.";
        }

        return redirect()->route('pharmacies.index')
            ->with('success', $message)
            ->with('import_errors', $result['errors']);
    }
}
