<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with(['pharmacy.zone', 'commercial'])->orderByDesc('created_at');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('pharmacy', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%$search%")
                        ->orWhere('owner_name', 'like', "%$search%")
                        ->orWhere('phone', 'like', "%$search%")
                        ->orWhereHas('zone', function ($zq) use ($search) {
                            $zq->where('name', 'like', "%$search%");
                        });
                })->orWhereHas('commercial', function ($cq) use ($search) {
                    $cq->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                });
            });
        }

        if ($zoneId = $request->get('zone_id')) {
            $query->whereHas('pharmacy.zone', fn ($q) => $q->where('id', $zoneId));
        }

        $clients = $query->paginate(15)->withQueryString();

        // Pour les filtres côté vue
        $zones = \App\Models\Zone::where('status', 'active')->orderBy('name')->get();

        return view('clients.index', compact('clients', 'zones'));
    }

    public function show(Client $client)
    {
        $client->load(['pharmacy.zone', 'commercial', 'creator']);
        return view('clients.show', compact('client'));
    }

    public function createFromPharmacy(Pharmacy $pharmacy)
    {
        $pharmacy->load(['zone', 'client.commercial']);

        if ($pharmacy->client) {
            return redirect()
                ->route('clients.show', $pharmacy->client)
                ->with('success', 'Cette pharmacie a déjà été convertie en client.');
        }

        $commercials = User::role('Commercial')->orderBy('name')->get();

        return view('clients.convert_from_pharmacy', compact('pharmacy', 'commercials'));
    }

    public function storeFromPharmacy(Request $request, Pharmacy $pharmacy)
    {
        $user = Auth::user();

        if ($pharmacy->client) {
            return redirect()
                ->route('clients.show', $pharmacy->client)
                ->with('success', 'Cette pharmacie a déjà été convertie en client.');
        }

        if (!$user->hasRole('Admin') && !$user->hasRole('Superviseur') && !$user->hasRole('Commercial')) {
            abort(403);
        }

        $validated = $request->validate([
            'payment_terms' => 'required|string|max:2000',
            'credit_limit'  => 'required|numeric|min:0',
            'commercial_id' => 'required|exists:users,id',
        ]);

        $client = Client::create([
            'pharmacy_id'    => $pharmacy->id,
            'payment_terms'  => $validated['payment_terms'],
            'credit_limit'   => $validated['credit_limit'],
            'commercial_id'  => (int) $validated['commercial_id'],
            'status'          => 'actif',
            'created_by'      => $user->id,
        ]);

        // Met à jour les champs existants côté pharmacie pour refléter la conversion
        $pharmacy->update([
            'interest_status' => 'client',
            'partnership_type' => 'client_direct',
        ]);

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Pharmacie convertie en client.');
    }
}

