@extends('layouts.app')

@section('title', 'Nouveau client')
@section('page_title', 'Clients')

@section('content')
<div style="max-width:720px;">
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-muted);margin-bottom:20px;flex-wrap:wrap;">
        <a href="{{ route('clients.index') }}" style="color:var(--primary);text-decoration:none;">Clients</a>
        <i class="bi bi-chevron-right" style="font-size:11px;"></i>
        <span>Nouveau client</span>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Associer une pharmacie</h2>
            <p style="font-size:13px;color:var(--text-secondary);margin-top:4px;">Choisissez une pharmacie sans fiche client, puis renseignez les conditions commerciales.</p>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;margin-bottom:20px;padding:12px 16px;border-radius:8px;">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <ul style="margin:8px 0 0 18px;padding:0;">
                        @foreach($errors->all() as $error)
                            <li style="font-size:13px;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($pharmacies->isEmpty())
                <p style="font-size:14px;color:var(--text-secondary);margin-bottom:16px;">Aucune pharmacie disponible (toutes sont déjà clients ou la liste est vide).</p>
                <a href="{{ route('pharmacies.index') }}" class="btn btn-outline"><i class="bi bi-hospital"></i> Pharmacies</a>
            @else
                <form method="POST" action="{{ route('clients.store') }}">
                    @csrf

                    <div style="margin-bottom:18px;">
                        <label style="display:block;font-size:12px;font-weight:700;margin-bottom:6px;">Pharmacie <span style="color:#dc2626">*</span></label>
                        <select name="pharmacy_id" required
                                style="width:100%;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;background:#fff;">
                            <option value="">— Choisir une pharmacie —</option>
                            @foreach($pharmacies as $p)
                                <option value="{{ $p->id }}" {{ old('pharmacy_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} — {{ $p->zone->name ?? '—' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom:18px;">
                        <label style="display:block;font-size:12px;font-weight:700;margin-bottom:6px;">Conditions de paiement <span style="color:#dc2626">*</span></label>
                        <textarea name="payment_terms" rows="3" required placeholder="Ex: Net 30, comptant…"
                                  style="width:100%;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;resize:vertical;">{{ old('payment_terms') }}</textarea>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px;">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;margin-bottom:6px;">Limite crédit <span style="color:#dc2626">*</span></label>
                            <input type="number" step="0.01" min="0" name="credit_limit" value="{{ old('credit_limit', '0') }}" required
                                   style="width:100%;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;">
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;margin-bottom:6px;">Commercial <span style="color:#dc2626">*</span></label>
                            <select name="commercial_id" required
                                    style="width:100%;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;background:#fff;">
                                <option value="">— Choisir —</option>
                                @foreach($commercials as $c)
                                    <option value="{{ $c->id }}" {{ old('commercial_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="display:flex;gap:10px;flex-wrap:wrap;">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Enregistrer</button>
                        <a href="{{ route('clients.index') }}" class="btn btn-outline">Annuler</a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
