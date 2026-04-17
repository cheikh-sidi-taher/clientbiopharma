@extends('layouts.app')

@section('title', 'Nouvelle Pharmacie')
@section('page_title', 'Pharmacies')

@section('content')

<div style="max-width:800px;">

    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-muted);margin-bottom:20px;">
        <a href="{{ route('pharmacies.index') }}" style="color:var(--primary);text-decoration:none;">Pharmacies</a>
        <i class="bi bi-chevron-right" style="font-size:11px;"></i>
        <span>Nouvelle pharmacie</span>
    </div>

    <div class="card">
        <div class="card-header" style="padding:20px 24px 0;">
            <h2 class="card-title">Enregistrer une pharmacie</h2>
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

            <form method="POST" action="{{ route('pharmacies.store') }}" id="pharmacy-form">
                @csrf

                {{-- Section 1: Informations de base --}}
                <div style="margin-bottom:24px;">
                    <h3 style="font-size:13px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid var(--border);padding-bottom:8px;margin-bottom:16px;">
                        <i class="bi bi-info-circle"></i> Informations générales
                    </h3>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">

                        <div style="grid-column:1/-1;">
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Nom de la pharmacie <span style="color:#dc2626">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   placeholder="Ex: Pharmacie Al Amal"
                                   style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;"
                                   onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">
                        </div>

                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Responsable</label>
                            <input type="text" name="owner_name" value="{{ old('owner_name') }}"
                                   placeholder="Nom du pharmacien"
                                   style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;"
                                   onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">
                        </div>

                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Téléphone</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   placeholder="Ex: 22 xx xx xx"
                                   style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;"
                                   onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">
                        </div>

                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Zone <span style="color:#dc2626">*</span></label>
                            <select name="zone_id" required
                                    style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;background:#fff;outline:none;cursor:pointer;">
                                <option value="">— Sélectionner une zone —</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Type</label>
                            <select name="type"
                                    style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;background:#fff;outline:none;cursor:pointer;">
                                <option value="privée" {{ old('type','privée') === 'privée' ? 'selected' : '' }}>Privée</option>
                                <option value="publique" {{ old('type') === 'publique' ? 'selected' : '' }}>Publique</option>
                                <option value="clinique" {{ old('type') === 'clinique' ? 'selected' : '' }}>Clinique</option>
                            </select>
                        </div>

                        <div style="grid-column:1/-1;">
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Adresse</label>
                            <input type="text" name="address" value="{{ old('address') }}"
                                   placeholder="Quartier, rue..."
                                   style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;"
                                   onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                    </div>
                </div>

                {{-- Section 2: Analyse terrain --}}
                <div style="margin-bottom:24px;">
                    <h3 style="font-size:13px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid var(--border);padding-bottom:8px;margin-bottom:16px;">
                        <i class="bi bi-clipboard2-pulse"></i> Analyse terrain
                    </h3>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Produits les plus vendus</label>
                        <textarea name="best_selling_products" rows="2" placeholder="Ex: antibiotiques, vitamines..."
                                  style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;resize:vertical;"
                                  onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">{{ old('best_selling_products') }}</textarea>
                    </div>

                    {{-- Checkboxes besoins --}}
                    <div style="margin-top:14px;display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        @php
                            $checkboxes = [
                                'stock_problem'     => ['Problème de stock', 'bi-box-seam', '#dc2626'],
                                'delivery_problem'  => ['Problème de livraison', 'bi-truck', '#f59e0b'],
                                'training_need'     => ['Besoin formation', 'bi-mortarboard', '#3b82f6'],
                                'distribution_need' => ['Besoin distribution', 'bi-grid', '#8b5cf6'],
                            ];
                        @endphp
                        @foreach($checkboxes as $name => [$label, $icon, $color])
                        <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;cursor:pointer;transition:border-color .2s;"
                               onmouseover="this.style.borderColor='{{ $color }}'" onmouseout="this.style.borderColor='{{ old($name) ? $color : '#e2e8f0' }}'">
                            <input type="checkbox" name="{{ $name }}" value="1" {{ old($name) ? 'checked' : '' }}
                                   style="width:16px;height:16px;accent-color:{{ $color }};cursor:pointer;">
                            <i class="bi {{ $icon }}" style="color:{{ $color }};font-size:16px;"></i>
                            <span style="font-size:13px;font-weight:500;color:var(--text-primary);">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Section 3: Statut commercial --}}
                <div style="margin-bottom:24px;">
                    <h3 style="font-size:13px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid var(--border);padding-bottom:8px;margin-bottom:16px;">
                        <i class="bi bi-graph-up"></i> Statut commercial
                    </h3>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Niveau d'intérêt</label>
                            <select name="interest_status" required
                                    style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;background:#fff;outline:none;cursor:pointer;">
                                @foreach(\App\Models\Pharmacy::$interestLabels as $val => $label)
                                    <option value="{{ $val }}" {{ old('interest_status','non_visité') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Type partenariat</label>
                            <select name="partnership_type"
                                    style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;background:#fff;outline:none;cursor:pointer;">
                                <option value="aucun">Aucun</option>
                                <option value="distributeur">Distributeur</option>
                                <option value="partenaire">Partenaire</option>
                                <option value="client_direct">Client direct</option>
                            </select>
                        </div>
                        <div style="grid-column:1/-1;">
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Notes</label>
                            <textarea name="notes" rows="3" placeholder="Remarques sur la visite, besoins particuliers..."
                                      style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;resize:vertical;"
                                      onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div style="display:flex;gap:10px;">
                    <button type="submit" class="btn btn-primary" id="submit-pharmacy">
                        <i class="bi bi-check-circle-fill"></i> Enregistrer la pharmacie
                    </button>
                    <a href="{{ route('pharmacies.index') }}" class="btn btn-outline">
                        <i class="bi bi-x"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
