@extends('layouts.app')

@section('title', 'Convertir en client')
@section('page_title', 'Clients')

@section('content')
<div style="max-width:900px;">
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-muted);margin-bottom:20px;flex-wrap:wrap;">
        <a href="{{ route('pharmacies.index') }}" style="color:var(--primary);text-decoration:none;">Pharmacies</a>
        <i class="bi bi-chevron-right" style="font-size:11px;"></i>
        <span>Convertir en client</span>
    </div>

    <div class="card" style="margin-bottom:20px;">
        <div class="card-body">
            <div style="margin-bottom:16px;">
                <h2 style="font-size:18px;font-weight:900;color:var(--text-primary);margin-bottom:6px;">
                    {{ $pharmacy->name }}
                </h2>
                <p style="font-size:13px;color:var(--text-secondary);">
                    Zone : {{ $pharmacy->zone->name ?? '—' }} | Responsable : {{ $pharmacy->owner_name ?? '—' }}
                </p>
            </div>

            @if($errors->any())
                <div class="alert" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;margin-bottom:20px;">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <ul style="margin:0 0 0 18px;padding:0;">
                        @foreach($errors->all() as $error)
                            <li style="font-size:13px;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('pharmacies.convert_to_client.store', $pharmacy) }}">
                @csrf

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                    <div style="grid-column:1/-1;">
                        <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">
                            Conditions de paiement <span style="color:#dc2626">*</span>
                        </label>
                        <textarea name="payment_terms" rows="3" required
                                  placeholder="Ex: Net 30, Comptant à la commande, etc."
                                  style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;resize:vertical;"
                                  onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">{{ old('payment_terms') }}</textarea>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">
                            Limite crédit <span style="color:#dc2626">*</span>
                        </label>
                        <input type="number" step="0.01" min="0" name="credit_limit" value="{{ old('credit_limit') }}" required
                               style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;"
                               onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">
                            Commercial assigné <span style="color:#dc2626">*</span>
                        </label>
                        <select name="commercial_id" required
                                style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;background:#fff;outline:none;cursor:pointer;">
                            <option value="">— Choisir —</option>
                            @foreach($commercials as $commercial)
                                <option value="{{ $commercial->id }}" {{ old('commercial_id') == $commercial->id ? 'selected' : '' }}>
                                    {{ $commercial->name }} ({{ $commercial->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-arrow-right-circle-fill"></i> Convertir en client
                    </button>
                    <a href="{{ route('pharmacies.show', $pharmacy) }}" class="btn btn-outline">
                        <i class="bi bi-x"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

