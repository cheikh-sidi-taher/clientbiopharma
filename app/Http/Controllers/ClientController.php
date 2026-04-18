<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Pharmacy;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $clients = Client::query()
            ->filtered($request)
            ->paginate(15)
            ->withQueryString();

        $zones = Zone::where('status', 'active')->orderBy('name')->get();
        $commercials = User::role('Commercial')->orderBy('name')->get();

        return view('clients.index', compact('clients', 'zones', 'commercials'));
    }

    public function create(): View
    {
        $pharmacies = Pharmacy::query()
            ->whereDoesntHave('client')
            ->with('zone')
            ->orderBy('name')
            ->get();

        $commercials = User::role('Commercial')->orderBy('name')->get();

        return view('clients.create', compact('pharmacies', 'commercials'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pharmacy_id' => ['required', 'exists:pharmacies,id'],
            'payment_terms' => ['required', 'string', 'max:2000'],
            'credit_limit' => ['required', 'numeric', 'min:0'],
            'commercial_id' => ['required', 'exists:users,id'],
        ]);

        $pharmacy = Pharmacy::with('client')->findOrFail($validated['pharmacy_id']);

        if ($pharmacy->client) {
            return redirect()
                ->route('clients.create')
                ->withInput()
                ->withErrors(['pharmacy_id' => 'Cette pharmacie est déjà enregistrée comme client.']);
        }

        $user = Auth::user();
        $client = $this->persistNewClient($pharmacy, $validated, $user);

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Client créé.');
    }

    public function show(Client $client): View
    {
        $client->load(['pharmacy.zone', 'commercial', 'creator']);

        return view('clients.show', compact('client'));
    }

    public function edit(Client $client): View
    {
        $client->load(['pharmacy.zone']);
        $commercials = User::role('Commercial')->orderBy('name')->get();

        return view('clients.edit', compact('client', 'commercials'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'payment_terms' => ['required', 'string', 'max:2000'],
            'credit_limit' => ['required', 'numeric', 'min:0'],
            'commercial_id' => ['required', 'exists:users,id'],
            'status' => ['required', 'in:actif,inactif'],
        ]);

        $client->update([
            'payment_terms' => $validated['payment_terms'],
            'credit_limit' => $validated['credit_limit'],
            'commercial_id' => (int) $validated['commercial_id'],
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Client mis à jour.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        DB::transaction(function () use ($client) {
            $pharmacy = $client->pharmacy;
            $client->delete();
            $pharmacy->update([
                'interest_status' => 'intéressé',
                'partnership_type' => 'aucun',
            ]);
        });

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client supprimé. La pharmacie peut être à nouveau convertie.');
    }

    public function createFromPharmacy(Pharmacy $pharmacy): RedirectResponse|View
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

    public function storeFromPharmacy(Request $request, Pharmacy $pharmacy): RedirectResponse
    {
        $user = Auth::user();

        if ($pharmacy->client) {
            return redirect()
                ->route('clients.show', $pharmacy->client)
                ->with('success', 'Cette pharmacie a déjà été convertie en client.');
        }

        if (! $user->hasRole('Admin') && ! $user->hasRole('Superviseur') && ! $user->hasRole('Commercial')) {
            abort(403);
        }

        $validated = $request->validate([
            'payment_terms' => 'required|string|max:2000',
            'credit_limit' => 'required|numeric|min:0',
            'commercial_id' => 'required|exists:users,id',
        ]);

        $client = $this->persistNewClient($pharmacy, $validated, $user);

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Pharmacie convertie en client.');
    }

    /**
     * @param  array{payment_terms: string, credit_limit: float|int|string, commercial_id: int|string}  $validated
     */
    private function persistNewClient(Pharmacy $pharmacy, array $validated, User $user): Client
    {
        $client = Client::create([
            'pharmacy_id' => $pharmacy->id,
            'payment_terms' => $validated['payment_terms'],
            'credit_limit' => $validated['credit_limit'],
            'commercial_id' => (int) $validated['commercial_id'],
            'status' => 'actif',
            'created_by' => $user->id,
        ]);

        $pharmacy->update([
            'interest_status' => 'client',
            'partnership_type' => 'client_direct',
        ]);

        return $client;
    }
}
