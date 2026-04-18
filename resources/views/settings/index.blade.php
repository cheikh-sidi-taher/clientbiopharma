@extends('layouts.app')

@section('title', 'Paramètres')
@section('page_title', 'Paramètres')

@section('content')

<div class="grid" style="grid-template-columns: 220px 1fr; gap: 24px; align-items: start;">
    <nav class="card" style="padding:12px 0; position:sticky; top:88px;">
        <a href="#compte" class="settings-nav-link" style="display:flex;align-items:center;gap:10px;padding:10px 16px;font-size:14px;font-weight:600;color:var(--text-primary);text-decoration:none;border-left:3px solid transparent;">
            <i class="bi bi-person-fill"></i> Mon compte
        </a>
        @if(auth()->user()->hasRole('Admin'))
            <a href="#entreprise" class="settings-nav-link" style="display:flex;align-items:center;gap:10px;padding:10px 16px;font-size:14px;font-weight:600;color:var(--text-primary);text-decoration:none;border-left:3px solid transparent;">
                <i class="bi bi-building"></i> Entreprise
            </a>
        @endif
    </nav>

    <div style="display:flex;flex-direction:column;gap:24px;">

        @if (session('status') === 'profile-updated')
            <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> Profil mis à jour.</div>
        @endif
        @if (session('status') === 'password-updated')
            <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> Mot de passe mis à jour.</div>
        @endif
        @if (session('status') === 'verification-link-sent')
            <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> Un nouveau lien de vérification a été envoyé.</div>
        @endif

        {{-- Mon compte --}}
        <section id="compte" class="card">
            <div class="card-header">
                <h2 class="card-title">Informations du profil</h2>
                <p style="font-size:13px;color:var(--text-secondary);margin-top:4px;">Nom et adresse e-mail de votre compte.</p>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')

                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Nom complet</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               style="width:100%;max-width:420px;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;">
                        @error('name')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>

                    <div style="margin-bottom:20px;">
                        <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">E-mail</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               style="width:100%;max-width:420px;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;">
                        @error('email')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding:9px 18px;">
                        <i class="bi bi-check-lg"></i> Enregistrer
                    </button>
                </form>
            </div>
        </section>

        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Mot de passe</h2>
                <p style="font-size:13px;color:var(--text-secondary);margin-top:4px;">Utilisez un mot de passe long et unique.</p>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')

                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Mot de passe actuel</label>
                        <input type="password" name="current_password" autocomplete="current-password"
                               style="width:100%;max-width:420px;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;">
                        @error('current_password', 'updatePassword')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>

                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Nouveau mot de passe</label>
                        <input type="password" name="password" autocomplete="new-password"
                               style="width:100%;max-width:420px;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;">
                        @error('password', 'updatePassword')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>

                    <div style="margin-bottom:20px;">
                        <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Confirmation</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password"
                               style="width:100%;max-width:420px;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;">
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding:9px 18px;">
                        <i class="bi bi-shield-lock"></i> Mettre à jour le mot de passe
                    </button>
                </form>
            </div>
        </section>

        <section class="card" style="border-color:#fecaca;">
            <div class="card-header">
                <h2 class="card-title" style="color:#b91c1c;">Supprimer le compte</h2>
                <p style="font-size:13px;color:var(--text-secondary);margin-top:4px;">Cette action est définitive.</p>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Supprimer définitivement votre compte ?');">
                    @csrf
                    @method('delete')
                    <p style="font-size:13px;color:var(--text-secondary);margin-bottom:12px;">Saisissez votre mot de passe pour confirmer.</p>
                    <div style="margin-bottom:12px;">
                        <input type="password" name="password" placeholder="Mot de passe actuel" required autocomplete="current-password"
                               style="width:100%;max-width:320px;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;">
                        @error('password', 'userDeletion')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="btn btn-outline" style="border-color:#fecaca;color:#b91c1c;">
                        <i class="bi bi-trash"></i> Supprimer mon compte
                    </button>
                </form>
            </div>
        </section>

        @if(auth()->user()->hasRole('Admin'))
        <section id="entreprise" class="card">
            <div class="card-header">
                <h2 class="card-title">Entreprise</h2>
                <p style="font-size:13px;color:var(--text-secondary);margin-top:4px;">Ces informations apparaissent en en-tête des exports PDF (pharmacies, rapports).</p>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('settings.application.update') }}">
                    @csrf
                    @method('put')

                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Raison sociale <span style="color:#dc2626">*</span></label>
                        <input type="text" name="company_name" value="{{ old('company_name', $application['company_name']) }}" required
                               style="width:100%;max-width:480px;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;">
                        @error('company_name')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>

                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Adresse</label>
                        <textarea name="company_address" rows="2"
                                  style="width:100%;max-width:480px;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;resize:vertical;">{{ old('company_address', $application['company_address']) }}</textarea>
                        @error('company_address')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>

                    <div style="display:flex;gap:20px;flex-wrap:wrap;margin-bottom:20px;">
                        <div style="flex:1;min-width:200px;">
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Téléphone</label>
                            <input type="text" name="company_phone" value="{{ old('company_phone', $application['company_phone']) }}"
                                   style="width:100%;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;">
                            @error('company_phone')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                        </div>
                        <div style="flex:1;min-width:200px;">
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">E-mail</label>
                            <input type="email" name="company_email" value="{{ old('company_email', $application['company_email']) }}"
                                   style="width:100%;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;">
                            @error('company_email')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding:9px 18px;">
                        <i class="bi bi-building"></i> Enregistrer l’entreprise
                    </button>
                </form>
            </div>
        </section>
        @endif
    </div>
</div>

<style>
@media (max-width: 900px) {
    .grid[style*="grid-template-columns"] { grid-template-columns: 1fr !important; }
    nav.card { position: static !important; display: flex; flex-wrap: wrap; gap: 8px; padding: 12px !important; }
    .settings-nav-link { border-radius: 8px; border: 1px solid var(--border) !important; border-left: 1px solid var(--border) !important; }
}
</style>
@endsection
