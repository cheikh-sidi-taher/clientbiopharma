@extends('layouts.app')

@section('title', 'Modifier ' . $pharmacy->name)
@section('page_title', 'Pharmacies')

@section('content')

<div style="max-width:800px;">

    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-muted);margin-bottom:20px;">
        <a href="{{ route('pharmacies.index') }}" style="color:var(--primary);text-decoration:none;">Pharmacies</a>
        <i class="bi bi-chevron-right" style="font-size:11px;"></i>
        <a href="{{ route('pharmacies.show', $pharmacy) }}" style="color:var(--primary);text-decoration:none;">{{ $pharmacy->name }}</a>
        <i class="bi bi-chevron-right" style="font-size:11px;"></i>
        <span>Modifier</span>
    </div>

    <div class="card">
        <div class="card-header" style="padding:20px 24px 0;">
            <h2 class="card-title">Modifier : {{ $pharmacy->name }}</h2>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;margin-bottom:20px;">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <ul style="margin:0 0 0 8px;padding:0;list-style:none;">
                        @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('pharmacies.update', $pharmacy) }}">
                @csrf @method('PUT')

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;">
                    <div style="grid-column:1/-1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Nom <span style="color:#dc2626">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $pharmacy->name) }}" required
                               style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;"
                               onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Responsable</label>
                        <input type="text" name="owner_name" value="{{ old('owner_name', $pharmacy->owner_name) }}"
                               style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;"
                               onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Téléphone</label>
                        <input type="text" name="phone" value="{{ old('phone', $pharmacy->phone) }}"
                               style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;"
                               onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Zone <span style="color:#dc2626">*</span></label>
                        <select name="zone_id" required style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;background:#fff;outline:none;">
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" {{ old('zone_id', $pharmacy->zone_id) == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Type</label>
                        <select name="type" style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;background:#fff;outline:none;">
                            @foreach(['privée','publique','clinique'] as $t)
                                <option value="{{ $t }}" {{ old('type', $pharmacy->type) === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="grid-column:1/-1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Adresse</label>
                        <input type="text" name="address" value="{{ old('address', $pharmacy->address) }}"
                               style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;"
                               onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                    <div style="grid-column:1/-1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Produits les plus vendus</label>
                        <textarea name="best_selling_products" rows="2"
                                  style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;resize:vertical;"
                                  onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">{{ old('best_selling_products', $pharmacy->best_selling_products) }}</textarea>
                    </div>
                </div>

                {{-- Checkboxes --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:20px;">
                    @php
                        $checkboxes = ['stock_problem'=>['Problème de stock','#dc2626'],'delivery_problem'=>['Problème livraison','#f59e0b'],'training_need'=>['Besoin formation','#3b82f6'],'distribution_need'=>['Besoin distribution','#8b5cf6']];
                    @endphp
                    @foreach($checkboxes as $name => [$label, $color])
                    <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;cursor:pointer;">
                        <input type="checkbox" name="{{ $name }}" value="1" {{ old($name, $pharmacy->$name) ? 'checked' : '' }}
                               style="width:16px;height:16px;accent-color:{{ $color }};cursor:pointer;">
                        <span style="font-size:13px;font-weight:500;">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;">
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Statut d'intérêt</label>
                        <select name="interest_status" style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;background:#fff;outline:none;">
                            @foreach(\App\Models\Pharmacy::$interestLabels as $val => $label)
                                <option value="{{ $val }}" {{ old('interest_status', $pharmacy->interest_status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Type partenariat</label>
                        <select name="partnership_type" style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;background:#fff;outline:none;">
                            @foreach(['aucun','distributeur','partenaire','client_direct'] as $pt)
                                <option value="{{ $pt }}" {{ old('partnership_type', $pharmacy->partnership_type) === $pt ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$pt)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="grid-column:1/-1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Notes</label>
                        <textarea name="notes" rows="3"
                                  style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;resize:vertical;"
                                  onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">{{ old('notes', $pharmacy->notes) }}</textarea>
                    </div>
                </div>

                <div style="display:flex;gap:10px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle-fill"></i> Mettre à jour
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
