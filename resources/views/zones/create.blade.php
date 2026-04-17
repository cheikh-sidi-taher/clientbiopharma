@extends('layouts.app')

@section('title', 'Nouvelle Zone')
@section('page_title', 'Zones')

@section('content')

<div style="max-width:640px;">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-muted);margin-bottom:20px;">
        <a href="{{ route('zones.index') }}" style="color:var(--primary);text-decoration:none;">Zones</a>
        <i class="bi bi-chevron-right" style="font-size:11px;"></i>
        <span>Nouvelle zone</span>
    </div>

    <div class="card">
        <div class="card-header" style="padding:20px 24px 0;">
            <div>
                <h2 class="card-title">Créer une nouvelle zone</h2>
                <p style="font-size:13px;color:var(--text-secondary);margin-top:4px;">Définissez une zone géographique pour organiser les pharmacies.</p>
            </div>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;margin-bottom:20px;">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <ul style="margin:0 0 0 8px; padding:0; list-style:none;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('zones.store') }}" id="zone-form">
                @csrf

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div style="grid-column:1/-1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                            Nom de la zone <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                               placeholder="Ex: Tevragh Zeina"
                               style="width:100%;padding:11px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;transition:border-color .2s;"
                               onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'"
                               required>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Statut</label>
                        <select name="status" id="status"
                                style="width:100%;padding:11px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;background:#fff;cursor:pointer;">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Objectif pharmacies</label>
                        <input type="number" name="target_pharmacies" id="target_pharmacies" value="{{ old('target_pharmacies', 0) }}"
                               min="0" placeholder="Ex: 30"
                               style="width:100%;padding:11px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;transition:border-color .2s;"
                               onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>

                    <div style="grid-column:1/-1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Agent assigné</label>
                        <select name="agent_id" id="agent_id"
                                style="width:100%;padding:11px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;background:#fff;cursor:pointer;">
                            <option value="">— Aucun agent assigné —</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" {{ old('agent_id') == $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="grid-column:1/-1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Description</label>
                        <textarea name="description" id="description" rows="3" placeholder="Description de la zone..."
                                  style="width:100%;padding:11px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;resize:vertical;transition:border-color .2s;"
                                  onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div style="display:flex;gap:10px;margin-top:8px;">
                    <button type="submit" class="btn btn-primary" id="submit-zone">
                        <i class="bi bi-check-circle-fill"></i> Enregistrer la zone
                    </button>
                    <a href="{{ route('zones.index') }}" class="btn btn-outline">
                        <i class="bi bi-x"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
