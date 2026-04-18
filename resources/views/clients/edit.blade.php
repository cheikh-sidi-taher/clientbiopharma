@extends('layouts.app')

@section('title', 'Modifier client')
@section('page_title', 'Clients')

@section('content')
<div style="max-width:720px;">
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-muted);margin-bottom:20px;flex-wrap:wrap;">
        <a href="{{ route('clients.index') }}" style="color:var(--primary);text-decoration:none;">Clients</a>
        <i class="bi bi-chevron-right" style="font-size:11px;"></i>
        <a href="{{ route('clients.show', $client) }}" style="color:var(--primary);text-decoration:none;">{{ $client->pharmacy->name ?? 'Client' }}</a>
        <i class="bi bi-chevron-right" style="font-size:11px;"></i>
        <span>Modifier</span>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">{{ $client->pharmacy->name ?? 'Client' }}</h2>
            <p style="font-size:13px;color:var(--text-secondary);margin-top:4px;">Zone : {{ $client->pharmacy->zone->name ?? '—' }}</p>
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

            <form method="POST" action="{{ route('clients.update', $client) }}">
                @csrf
                @method('put')

                <div style="margin-bottom:18px;">
                    <label style="display:block;font-size:12px;font-weight:700;margin-bottom:6px;">Conditions de paiement <span style="color:#dc2626">*</span></label>
                    <textarea name="payment_terms" rows="3" required
                              style="width:100%;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;resize:vertical;">{{ old('payment_terms', $client->payment_terms) }}</textarea>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:700;margin-bottom:6px;">Limite crédit <span style="color:#dc2626">*</span></label>
                        <input type="number" step="0.01" min="0" name="credit_limit" value="{{ old('credit_limit', $client->credit_limit) }}" required
                               style="width:100%;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:700;margin-bottom:6px;">Statut <span style="color:#dc2626">*</span></label>
                        <select name="status" required
                                style="width:100%;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;background:#fff;">
                            <option value="actif" {{ old('status', $client->status) === 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="inactif" {{ old('status', $client->status) === 'inactif' ? 'selected' : '' }}>Inactif</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom:22px;">
                    <label style="display:block;font-size:12px;font-weight:700;margin-bottom:6px;">Commercial assigné <span style="color:#dc2626">*</span></label>
                    <select name="commercial_id" required
                            style="width:100%;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;background:#fff;">
                        @foreach($commercials as $c)
                            <option value="{{ $c->id }}" {{ old('commercial_id', $client->commercial_id) == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Enregistrer</button>
                    <a href="{{ route('clients.show', $client) }}" class="btn btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
